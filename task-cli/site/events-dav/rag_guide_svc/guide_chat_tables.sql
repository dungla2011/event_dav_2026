-- Lịch sử chat của Trợ lý AI theo user đăng nhập (giống Conversation/Message của ChatAIV2 trên glxv3).
-- Chạy một lần trên DB glx_event. GuideChatController ghi vào đây.
CREATE TABLE IF NOT EXISTS guide_chat_conversations (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    BIGINT UNSIGNED NOT NULL,
    name       VARCHAR(255) NULL COMMENT 'câu hỏi đầu tiên, cắt 100 ký tự',
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY idx_user_updated (user_id, updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS guide_chat_messages (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    role            VARCHAR(16) NOT NULL COMMENT 'user | assistant',
    content         MEDIUMTEXT NOT NULL,
    created_at      DATETIME NULL,
    KEY idx_conv (conversation_id, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
