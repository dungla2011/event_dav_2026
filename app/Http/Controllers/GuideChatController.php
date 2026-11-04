<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Widget "Trợ lý AI" ở góc phải trang quản trị: chuyển câu hỏi sang service Python
 * task-cli/site/events-dav/rag_guide_svc/guide_api.py (127.0.0.1:50010), service đó ghép
 * tài liệu rag_guide/*.txt vào prompt rồi hỏi model.
 *
 * Lịch sử chat lưu theo user đang đăng nhập (bảng guide_chat_conversations / guide_chat_messages,
 * tạo bằng rag_guide_svc/guide_chat_tables.sql), nên mở ở máy/trình duyệt khác vẫn thấy.
 * Ngữ cảnh gửi cho model lấy từ DB, không tin lịch sử do trình duyệt gửi lên.
 */
class GuideChatController extends Controller
{
    const SERVICE_URL = 'http://127.0.0.1:50010/chat';

    const PER_MINUTE = 15;

    const CONTEXT_TURNS = 12;   // số tin nhắn gần nhất của cuộc trò chuyện gửi kèm câu hỏi

    const HISTORY_LIMIT = 60;   // số tin nhắn trả về khi mở widget

    /** Cuộc trò chuyện gần nhất của user (hoặc ?conversation_id=), kèm tin nhắn. */
    public function history(Request $request)
    {
        $uid = auth()->id();
        $q = DB::table('guide_chat_conversations')->where('user_id', $uid);
        $conv = $request->filled('conversation_id')
            ? $q->where('id', (int)$request->conversation_id)->first()
            : $q->orderByDesc('updated_at')->orderByDesc('id')->first();
        if (! $conv) {
            return response()->json(['conversation_id' => null, 'messages' => []]);
        }

        $messages = DB::table('guide_chat_messages')
            ->where('conversation_id', $conv->id)
            ->orderByDesc('id')->limit(self::HISTORY_LIMIT)
            ->get(['role', 'content', 'created_at'])
            ->reverse()->values()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content, 'ts' => strtotime($m->created_at) * 1000]);

        return response()->json(['conversation_id' => $conv->id, 'messages' => $messages]);
    }

    public function chat(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_id' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $key = 'guide-chat:'.$user->id;
        if (RateLimiter::tooManyAttempts($key, self::PER_MINUTE)) {
            return response()->json(['error' => 'Bạn hỏi nhanh quá, vui lòng chờ một chút rồi hỏi tiếp.'], 429);
        }
        RateLimiter::hit($key, 60);

        $question = trim($data['message']);
        $conv = empty($data['conversation_id']) ? null : DB::table('guide_chat_conversations')
            ->where('id', $data['conversation_id'])->where('user_id', $user->id)->first();

        $context = $conv ? DB::table('guide_chat_messages')
            ->where('conversation_id', $conv->id)
            ->orderByDesc('id')->limit(self::CONTEXT_TURNS)
            ->get(['role', 'content'])->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])->values()->all() : [];
        $context[] = ['role' => 'user', 'content' => $question];

        $fail = fn ($code) => response()->json(['error' => 'Trợ lý AI đang tạm ngừng, vui lòng thử lại sau.'], $code);
        try {
            $res = Http::timeout(90)->post(self::SERVICE_URL, [
                'messages' => $context,
                'user' => $user->id.' '.($user->email ?? $user->name ?? ''),
            ]);
        } catch (\Throwable $e) {
            return $fail(503);
        }
        $reply = $res->ok() ? $res->json('reply') : null;
        if (! is_string($reply) || $reply === '') {
            return $fail(502);
        }

        // Chỉ lưu khi có câu trả lời, để câu hỏi lỗi không nằm lại trong lịch sử.
        $now = now();
        $convId = $conv->id ?? DB::table('guide_chat_conversations')->insertGetId([
            'user_id' => $user->id, 'name' => mb_substr($question, 0, 100), 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('guide_chat_messages')->insert([
            ['conversation_id' => $convId, 'user_id' => $user->id, 'role' => 'user', 'content' => $question, 'created_at' => $now],
            ['conversation_id' => $convId, 'user_id' => $user->id, 'role' => 'assistant', 'content' => $reply, 'created_at' => now()],
        ]);
        if ($conv) {
            DB::table('guide_chat_conversations')->where('id', $convId)->update(['updated_at' => $now]);
        }

        return response()->json(['reply' => $reply, 'conversation_id' => $convId]);
    }
}
