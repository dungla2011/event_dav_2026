#!/usr/bin/env python3
"""
Script chuyên dụng để convert OBJ+MTL+JPG sang GLB với texture đầy đủ
Sử dụng pygltflib để tạo GLB với texture embedded
"""

import os
import sys
import trimesh
import numpy as np
from PIL import Image
import base64
import json
from pathlib import Path

def load_texture_image(image_path):
    """Load texture image thành PIL Image object"""
    try:
        img = Image.open(image_path)
        
        # Convert sang RGB nếu cần
        if img.mode != 'RGB':
            img = img.convert('RGB')
        
        # Resize nếu quá lớn (để giảm kích thước file)
        max_size = 1024
        if max(img.size) > max_size:
            img.thumbnail((max_size, max_size), Image.Resampling.LANCZOS)
        
        print(f"✅ Texture loaded: {image_path} ({img.size[0]}x{img.size[1]})")
        return img
        
    except Exception as e:
        print(f"❌ Lỗi load texture {image_path}: {str(e)}")
        return None

def convert_obj_with_texture(obj_file, mtl_file=None, texture_file=None, output_file=None):
    """
    Convert OBJ với texture thành GLB
    """
    try:
        print(f"🎯 Converting: {obj_file}")
        print("=" * 50)
        
        # Tự động tìm MTL và texture files
        base_name = Path(obj_file).stem
        
        if mtl_file is None:
            mtl_file = f"{base_name}.mtl"
        if texture_file is None:
            texture_file = f"{base_name}.jpg"
        
        if output_file is None:
            output_file = f"files-3d/{base_name}_textured.glb"
        
        # Kiểm tra files tồn tại
        if not os.path.exists(obj_file):
            print(f"❌ Không tìm thấy OBJ: {obj_file}")
            return None
            
        print(f"📁 Files:")
        print(f"  - OBJ: {obj_file} ({'✅' if os.path.exists(obj_file) else '❌'})")
        print(f"  - MTL: {mtl_file} ({'✅' if os.path.exists(mtl_file) else '❌'})")
        print(f"  - TEX: {texture_file} ({'✅' if os.path.exists(texture_file) else '❌'})")
        
        # Load mesh
        print(f"\n🔄 Loading mesh...")
        mesh = trimesh.load(obj_file)
        
        if isinstance(mesh, trimesh.Scene):
            print(f"  - Scene với {len(mesh.geometry)} objects")
            # Lấy mesh chính
            mesh_keys = list(mesh.geometry.keys())
            if mesh_keys:
                mesh = mesh.geometry[mesh_keys[0]]
            else:
                mesh = mesh.dump().sum()
        
        print(f"  - Vertices: {len(mesh.vertices):,}")
        print(f"  - Faces: {len(mesh.faces):,}")
        print(f"  - Has UV: {'✅' if hasattr(mesh.visual, 'uv') else '❌'}")
        
        # Load texture nếu có
        texture_image = None
        if os.path.exists(texture_file):
            print(f"\n🖼️  Loading texture...")
            texture_image = load_texture_image(texture_file)
        
        # Tạo material với texture
        if texture_image:
            print(f"\n🎨 Applying texture...")
            
            # Tạo texture material
            material = trimesh.visual.material.PBRMaterial(
                name="TexturedMaterial",
                baseColorTexture=texture_image,
                metallicFactor=0.0,
                roughnessFactor=0.8
            )
            
            # Áp dụng material cho mesh
            if hasattr(mesh.visual, 'uv') and mesh.visual.uv is not None:
                mesh.visual.material = material
                print(f"  - ✅ Texture applied with UV mapping")
            else:
                # Tạo UV mapping đơn giản nếu không có
                print(f"  - ⚠️  Creating simple UV mapping...")
                uv = np.zeros((len(mesh.vertices), 2))
                # UV mapping đơn giản dựa trên tọa độ X,Z
                bounds = mesh.bounds
                uv[:, 0] = (mesh.vertices[:, 0] - bounds[0, 0]) / (bounds[1, 0] - bounds[0, 0])
                uv[:, 1] = (mesh.vertices[:, 2] - bounds[0, 2]) / (bounds[1, 2] - bounds[0, 2])
                mesh.visual.uv = uv
                mesh.visual.material = material
                print(f"  - ✅ Simple UV mapping created")
        
        # Export GLB
        print(f"\n💾 Exporting to: {output_file}")
        mesh.export(output_file)
        
        # Kiểm tra kết quả
        if os.path.exists(output_file):
            output_size = os.path.getsize(output_file)
            input_size = os.path.getsize(obj_file)
            texture_size_kb = os.path.getsize(texture_file) / 1024 if os.path.exists(texture_file) else 0
            
            print(f"\n✅ CONVERSION SUCCESS!")
            print(f"  - Input OBJ:  {input_size / (1024*1024):.1f} MB")
            print(f"  - Texture:    {texture_size_kb:.1f} KB")
            print(f"  - Output GLB: {output_size / (1024*1024):.1f} MB")
            print(f"  - Ratio:      {(output_size / input_size):.2f}x")
            
            return output_file
        else:
            print(f"❌ Export failed!")
            return None
            
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        import traceback
        traceback.print_exc()
        return None

def batch_convert():
    """Convert tất cả OBJ files có texture"""
    obj_files = ["files-3d/1.obj", "files-3d/2.obj", "files-3d/3.obj"]
    
    print("🚀 BATCH CONVERSION WITH TEXTURES")
    print("=" * 60)
    
    results = []
    
    for obj_file in obj_files:
        if not os.path.exists(obj_file):
            print(f"⚠️  Skip: {obj_file} (not found)")
            continue
            
        result = convert_obj_with_texture(obj_file)
        results.append({
            'input': obj_file,
            'output': result,
            'success': result is not None
        })
        
        print("\n" + "-" * 50)
    
    print(f"\n📋 FINAL SUMMARY:")
    print("=" * 60)
    
    for result in results:
        status = "✅" if result['success'] else "❌"
        print(f"{status} {result['input']} -> {result['output']}")
    
    successful = sum(1 for r in results if r['success'])
    print(f"\n🎯 Success rate: {successful}/{len(results)}")
    
    if successful > 0:
        print(f"\n📖 Hướng dẫn sử dụng:")
        print(f"  - Upload các file *_textured.glb lên web server")
        print(f"  - Các file này có texture embedded đầy đủ")
        print(f"  - Nên hiển thị màu sắc đúng trong web viewer")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        # Convert specific file
        obj_file = sys.argv[1]
        convert_obj_with_texture(obj_file)
    else:
        # Batch convert
        batch_convert()
