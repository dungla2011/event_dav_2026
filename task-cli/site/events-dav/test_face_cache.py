#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import requests
import json
import time

# Cấu hình
API_BASE = "http://localhost:5000"

def test_cache_status():
    """Test kiểm tra trạng thái cache"""
    print("\n🧪 TEST: CACHE_STATUS")
    
    url = f"{API_BASE}/cache_status"
    
    try:
        response = requests.get(url)
        print(f"📄 Status: {response.status_code}")
        result = response.json()
        
        if result.get('status') == 'success':
            data = result.get('data', {})
            total = data.get('total_entries', 0)
            print(f"✅ Success: Cache có {total} entries")
            
            # Hiển thị danh sách
            entries = data.get('entries', [])
            print("📋 Danh sách trong cache:")
            for entry in entries[:5]:  # Chỉ hiển thị 5 entry đầu
                print(f"   - ID: {entry.get('id')}, Name: {entry.get('name')}")
            if len(entries) > 5:
                print(f"   ... và {len(entries) - 5} entries khác")
                
        else:
            print(f"❌ Failed: {result.get('error')}")
            
    except Exception as e:
        print(f"🔥 Error: {str(e)}")

def test_reload_cache():
    """Test reload cache từ server"""
    print("\n🧪 TEST: RELOAD_CACHE")
    
    url = f"{API_BASE}/reload_face_cache"
    
    try:
        response = requests.post(url)
        print(f"📄 Status: {response.status_code}")
        result = response.json()
        
        if result.get('status') == 'success':
            message = result.get('message', '')
            print(f"✅ Success: {message}")
        else:
            print(f"❌ Failed: {result.get('error')}")
            
    except Exception as e:
        print(f"🔥 Error: {str(e)}")

def test_detect_with_server_cache():
    """Test detect face với cache từ server"""
    print("\n🧪 TEST: DETECT_FACE với Server Cache")
    
    # Tạo ảnh test
    from PIL import Image
    img = Image.new('RGB', (300, 300), color='lightgreen')
    img.save("test_server_cache.jpg")
    print("📸 Created test image: test_server_cache.jpg")
    
    url = f"{API_BASE}/detect_face"
    
    try:
        with open("test_server_cache.jpg", 'rb') as f:
            files = {'file': ('test.jpg', f, 'image/jpeg')}
            response = requests.post(url, files=files)
        
        print(f"📄 Status: {response.status_code}")
        result = response.json()
        
        if result.get('status') == 'success':
            data = result.get('data', {})
            print(f"✅ Success: Nhận diện được ID: {data.get('id')}")
            print(f"🔗 Confirm URL: {data.get('url_confirm')}")
        else:
            print(f"❌ Failed: {result.get('error')}")
            
    except Exception as e:
        print(f"🔥 Error: {str(e)}")

def test_direct_api_call():
    """Test gọi trực tiếp API lấy face list"""
    print("\n🧪 TEST: DIRECT API CALL")
    
    url = "https://events.dav.edu.vn/tool1/_site/event_mng/galaxy_face_detection/get-face-vector.php"
    
    try:
        response = requests.get(url, timeout=30)
        print(f"📄 Status: {response.status_code}")
        result = response.json()
        
        if result.get('status') == 'success':
            data = result.get('data', [])
            print(f"✅ Success: Server có {len(data)} face entries")
            
            # Hiển thị sample
            for i, entry in enumerate(data[:3]):
                print(f"   {i+1}. ID: {entry.get('id')}, Name: {entry.get('name')}")
                face_str = entry.get('face', '')
                if isinstance(face_str, str):
                    try:
                        face_vector = json.loads(face_str)
                        print(f"      Face vector: {len(face_vector)} dims")
                    except:
                        print(f"      Face vector: Invalid JSON")
                else:
                    print(f"      Face vector: {len(face_str)} dims")
                    
        else:
            print(f"❌ Failed: {result}")
            
    except Exception as e:
        print(f"🔥 Error: {str(e)}")

def check_server():
    """Kiểm tra server có chạy không"""
    try:
        response = requests.get(f"{API_BASE}/cache_status", timeout=5)
        return response.status_code == 200
    except:
        return False

def main():
    print("🚀 FACE CACHE TEST")
    print("=" * 40)
    
    # Test direct API call trước
    print("\n1. Testing direct API call...")
    test_direct_api_call()
    
    # Kiểm tra server
    if not check_server():
        print("❌ Server không chạy!")
        print("💡 Hãy chạy: python face_api.py")
        return
    
    print("\n✅ Server đang chạy")
    
    # Test cache functionality
    print("\n2. Testing cache status...")
    test_cache_status()
    
    print("\n3. Testing cache reload...")
    test_reload_cache()
    
    print("\n4. Testing detect with server cache...")
    test_detect_with_server_cache()
    
    print("\n🎉 Cache test hoàn tất!")

if __name__ == "__main__":
    main() 