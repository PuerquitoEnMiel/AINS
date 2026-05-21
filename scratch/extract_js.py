import re

with open('scratch/rendered.html', 'r', encoding='utf-8', errors='replace') as f:
    content = f.read()

# Extract inline script blocks (not external src ones)
scripts = re.findall(r'<script(?:\s[^>]*)?>([^<]*(?:(?!</script>)<[^<]*)*)</script>', content, re.DOTALL | re.IGNORECASE)

for i, s in enumerate(scripts):
    stripped = s.strip()
    if stripped:
        fname = f'scratch/extracted_script_{i+1}.js'
        with open(fname, 'w', encoding='utf-8', errors='replace') as out:
            out.write(stripped)
        print(f'Script {i+1}: {len(stripped)} chars -> {fname}')
    else:
        print(f'Script {i+1}: EMPTY (external src)')
