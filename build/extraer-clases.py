import re, glob
classes=set()
for f in glob.glob('resources/views/**/*.blade.php', recursive=True):
    s=open(f,encoding='utf-8',errors='replace').read()
    for m in re.findall(r'class="([^"]*)"', s):
        m2=re.sub(r'\{\{.*?\}\}',' ',m)
        for tok in m2.split():
            if tok and '$' not in tok and '@' not in tok and '{' not in tok:
                classes.add(tok)
    for m in re.findall(r"'([a-z0-9:\-\[\]#/. ]+)'", s):
        if any(p in m for p in ['bg-','text-','from-','to-','rounded','border','px-','py-','flex','grid','w-','h-']):
            for tok in m.split():
                if re.match(r'^[a-z0-9:\-\[\]#/.]+$', tok): classes.add(tok)
open('/tmp/classes.txt','w').write('\n'.join(sorted(classes)))
print("clases:", len(classes))
