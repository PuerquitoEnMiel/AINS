import sys
import re

with open('scratch/rendered.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Extract all script blocks
scripts = re.findall(r'<script(?:\s[^>]*)?>(.*?)</script>', content, re.DOTALL)
print(f"Found {len(scripts)} script blocks")

for i, s in enumerate(scripts):
    src_match = re.search(r'<script[^>]+src=', content)
    snippet = s[:100].replace('\n', ' ').strip()
    print(f"Script {i+1}: {len(s)} chars | Preview: {snippet[:80]}")
