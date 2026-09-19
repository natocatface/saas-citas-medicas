import re, glob

# ---------- paletas ----------
COLORS = {
 'white':'#ffffff','black':'#000000','transparent':'transparent',
 'slate-50':'#f8fafc','slate-100':'#f1f5f9','slate-200':'#e2e8f0','slate-300':'#cbd5e1','slate-400':'#94a3b8','slate-500':'#64748b','slate-600':'#475569','slate-700':'#334155','slate-800':'#1e293b','slate-900':'#0f172a',
 'brand-50':'#eef2ff','brand-100':'#e0e7ff','brand-200':'#c7d2fe','brand-300':'#a5b4fc','brand-400':'#818cf8','brand-500':'#6366f1','brand-600':'#4f46e5','brand-700':'#4338ca',
 'cyanx-300':'#67e8f9','cyanx-400':'#22d3ee','cyanx-500':'#17b8cf','cyanx-600':'#0e7490',
 'cyan-50':'#ecfeff','cyan-100':'#cffafe','cyan-200':'#a5f3fc','cyan-300':'#67e8f9','cyan-400':'#22d3ee','cyan-500':'#06b6d4','cyan-600':'#0891b2','cyan-700':'#0e7490',
 'emerald-50':'#ecfdf5','emerald-100':'#d1fae5','emerald-200':'#a7f3d0','emerald-300':'#6ee7b7','emerald-400':'#34d399','emerald-500':'#10b981','emerald-600':'#059669','emerald-700':'#047857',
 'amber-50':'#fffbeb','amber-100':'#fef3c7','amber-200':'#fde68a','amber-300':'#fcd34d','amber-400':'#fbbf24','amber-500':'#f59e0b','amber-600':'#d97706','amber-700':'#b45309',
 'rose-50':'#fff1f2','rose-100':'#ffe4e6','rose-200':'#fecdd3','rose-300':'#fda4af','rose-400':'#fb7185','rose-500':'#f43f5e','rose-600':'#e11d48','rose-700':'#be123c',
 'sky-50':'#f0f9ff','sky-100':'#e0f2fe','sky-200':'#bae6fd','sky-300':'#7dd3fc','sky-400':'#38bdf8','sky-500':'#0ea5e9','sky-600':'#0284c7','sky-700':'#0369a1',
 'violet-50':'#f5f3ff','violet-100':'#ede9fe','violet-200':'#ddd6fe','violet-400':'#a78bfa','violet-500':'#8b5cf6','violet-600':'#7c3aed','violet-700':'#6d28d9',
 'fuchsia-50':'#fdf4ff','fuchsia-100':'#fae8ff','fuchsia-400':'#e879f9','fuchsia-500':'#d946ef','fuchsia-600':'#c026d3',
 'indigo-50':'#eef2ff','indigo-100':'#e0e7ff','indigo-200':'#c7d2fe','indigo-400':'#818cf8','indigo-500':'#6366f1','indigo-600':'#4f46e5','indigo-700':'#4338ca',
 'teal-500':'#14b8a6',
}
SP = {'0':'0px','px':'1px','0.5':'0.125rem','1':'0.25rem','1.5':'0.375rem','2':'0.5rem','2.5':'0.625rem','3':'0.75rem','3.5':'0.875rem','4':'1rem','4.5':'1.125rem','5':'1.25rem','6':'1.5rem','7':'1.75rem','8':'2rem','9':'2.25rem','10':'2.5rem','11':'2.75rem','12':'3rem','14':'3.5rem','16':'4rem','20':'5rem','24':'6rem','28':'7rem','32':'8rem','40':'10rem','44':'11rem','48':'12rem','56':'14rem','60':'15rem','64':'16rem','72':'18rem','80':'20rem','96':'24rem','1/2':'50%','full':'100%','screen':'100vh','auto':'auto'}
FONT = {'text-[10px]':'10px','text-[11px]':'11px','text-[15px]':'15px','text-xs':'0.75rem','text-sm':'0.875rem','text-base':'1rem','text-lg':'1.125rem','text-xl':'1.25rem','text-2xl':'1.5rem','text-3xl':'1.875rem','text-4xl':'2.25rem','text-5xl':'3rem','text-6xl':'3.75rem'}
LH = {'text-xs':'1rem','text-sm':'1.25rem','text-base':'1.5rem','text-lg':'1.75rem','text-xl':'1.75rem','text-2xl':'2rem','text-3xl':'2.25rem','text-4xl':'2.5rem','text-5xl':'1','text-6xl':'1'}
WEIGHT={'font-normal':'400','font-medium':'500','font-semibold':'600','font-bold':'700','font-extrabold':'800','font-black':'900'}
RADIUS={'rounded':'0.25rem','rounded-md':'0.375rem','rounded-lg':'0.5rem','rounded-xl':'0.75rem','rounded-2xl':'1rem','rounded-3xl':'1.5rem','rounded-full':'9999px'}
SHADOW={'shadow-sm':'0 1px 2px 0 rgba(0,0,0,.05)','shadow':'0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px -1px rgba(0,0,0,.1)','shadow-md':'0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -2px rgba(0,0,0,.1)','shadow-lg':'0 10px 15px -3px rgba(0,0,0,.1),0 4px 6px -4px rgba(0,0,0,.1)','shadow-xl':'0 20px 25px -5px rgba(0,0,0,.1),0 8px 10px -6px rgba(0,0,0,.1)'}

def esc(c): # escapar para selector CSS
    return re.sub(r'([:\.\/\[\]#\(\)%])', r'\\\1', c)

def color_of(name):
    # admite slash alpha p.ej slate-900/50 ; y arbitrarios #hex
    if name.startswith('[') and name.endswith(']'):
        return name[1:-1]
    if '/' in name:
        base,al = name.rsplit('/',1)
        hexv = COLORS.get(base)
        if hexv and hexv.startswith('#'):
            h=hexv.lstrip('#'); r,g,b=int(h[0:2],16),int(h[2:4],16),int(h[4:6],16)
            return f'rgba({r},{g},{b},{int(al)/100})'
        return None
    return COLORS.get(name)

def decl(u):
    """Devuelve declaraciones CSS (sin selector) para la utilidad u, o None."""
    # display
    d={'flex':'display:flex','inline-flex':'display:inline-flex','grid':'display:grid','block':'display:block','inline-block':'display:inline-block','inline':'display:inline','hidden':'display:none','table':'display:table'}
    if u in d: return d[u]
    # flex / grid
    if u=='flex-1': return 'flex:1 1 0%'
    if u=='flex-col': return 'flex-direction:column'
    if u=='flex-row': return 'flex-direction:row'
    if u=='flex-wrap': return 'flex-wrap:wrap'
    if u=='shrink-0': return 'flex-shrink:0'
    if u=='flex-shrink-0': return 'flex-shrink:0'
    if u.startswith('items-'): return 'align-items:'+{'start':'flex-start','center':'center','end':'flex-end','baseline':'baseline','stretch':'stretch'}.get(u[6:],u[6:])
    if u.startswith('justify-'): return 'justify-content:'+{'start':'flex-start','center':'center','end':'flex-end','between':'space-between','around':'space-around','evenly':'space-evenly'}.get(u[8:],u[8:])
    if u.startswith('self-'): return 'align-self:'+u[5:]
    m=re.match(r'^grid-cols-(\d+)$',u)
    if m: return f'grid-template-columns:repeat({m.group(1)},minmax(0,1fr))'
    m=re.match(r'^col-span-(\d+)$',u)
    if m: return f'grid-column:span {m.group(1)}/span {m.group(1)}'
    m=re.match(r'^gap-(.+)$',u)
    if m and m.group(1) in SP: return 'gap:'+SP[m.group(1)]
    m=re.match(r'^gap-x-(.+)$',u)
    if m and m.group(1) in SP: return 'column-gap:'+SP[m.group(1)]
    m=re.match(r'^gap-y-(.+)$',u)
    if m and m.group(1) in SP: return 'row-gap:'+SP[m.group(1)]
    # spacing padding/margin
    m=re.match(r'^(-?)(p|m)([trblxy]?)-(.+)$',u)
    if m:
        neg,pm,side,val=m.groups()
        if val not in SP: 
            pass
        else:
            v=('-' if neg else '')+SP[val]
            prop='padding' if pm=='p' else 'margin'
            sides={'t':['top'],'b':['bottom'],'l':['left'],'r':['right'],'x':['left','right'],'y':['top','bottom'],'':['top','bottom','left','right']}[side]
            return ';'.join(f'{prop}-{s}:{v}' for s in sides)
    # space-y / space-x  (se maneja con selector hijo aparte)
    # sizing
    m=re.match(r'^(w|h|min-w|min-h|max-w|max-h)-(.+)$',u)
    if m:
        p,val=m.groups()
        prop={'w':'width','h':'height','min-w':'min-width','min-h':'min-height','max-w':'max-width','max-h':'max-height'}[p]
        if val.startswith('[') and val.endswith(']'): return f'{prop}:{val[1:-1]}'
        if val in SP: return f'{prop}:{SP[val]}'
        mw={'xs':'20rem','sm':'24rem','md':'28rem','lg':'32rem','xl':'36rem','2xl':'42rem','3xl':'48rem','4xl':'56rem','5xl':'64rem','6xl':'72rem','7xl':'80rem','none':'none','prose':'65ch'}
        if p=='max-w' and val in mw: return f'max-width:{mw[val]}'
    if u=='w-full':return 'width:100%'
    if u=='h-full':return 'height:100%'
    if u=='min-h-full':return 'min-height:100%'
    if u=='min-h-screen':return 'min-height:100vh'
    if u=='min-w-0':return 'min-width:0'
    # position
    if u in ('relative','absolute','fixed','sticky','static'): return 'position:'+u
    if u=='inset-0': return 'top:0;right:0;bottom:0;left:0'
    if u=='inset-x-0': return 'left:0;right:0'
    if u=='inset-y-0': return 'top:0;bottom:0'
    m=re.match(r'^(top|bottom|left|right)-(.+)$',u)
    if m and m.group(2) in SP: return f'{m.group(1)}:{SP[m.group(2)]}'
    m=re.match(r'^z-(\[?\d+\]?)$',u)
    if m: 
        z=m.group(1).strip('[]'); return f'z-index:{z}'
    # typography
    if u in FONT:
        css=f'font-size:{FONT[u]}'
        if u in LH: css+=f';line-height:{LH[u]}'
        return css
    if u in WEIGHT: return 'font-weight:'+WEIGHT[u]
    if u=='italic': return 'font-style:italic'
    if u=='uppercase': return 'text-transform:uppercase'
    if u=='capitalize': return 'text-transform:capitalize'
    if u=='text-center': return 'text-align:center'
    if u=='text-right': return 'text-align:right'
    if u=='text-left': return 'text-align:left'
    if u=='whitespace-nowrap': return 'white-space:nowrap'
    if u=='whitespace-pre-line': return 'white-space:pre-line'
    if u=='truncate': return 'overflow:hidden;text-overflow:ellipsis;white-space:nowrap'
    if u=='antialiased': return '-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale'
    if u=='tracking-wider': return 'letter-spacing:0.05em'
    if u=='tracking-tight': return 'letter-spacing:-0.025em'
    if u=='tracking-[0.2em]': return 'letter-spacing:0.2em'
    if u=='leading-none': return 'line-height:1'
    if u=='leading-tight': return 'line-height:1.25'
    if u=='leading-snug': return 'line-height:1.375'
    if u=='leading-relaxed': return 'line-height:1.625'
    if u=='leading-[1.05]': return 'line-height:1.05'
    if u=='font-mono': return "font-family:ui-monospace,SFMono-Regular,Menlo,monospace"
    if u=='font-sans': return "font-family:Inter,ui-sans-serif,system-ui,sans-serif"
    # colors
    m=re.match(r'^text-(.+)$',u)
    if m:
        c=color_of(m.group(1))
        if c: return 'color:'+c
    m=re.match(r'^bg-(.+)$',u)
    if m and not m.group(1).startswith('gradient'):
        c=color_of(m.group(1))
        if c: return 'background-color:'+c
    m=re.match(r'^border-(.+)$',u)
    if m:
        c=color_of(m.group(1))
        if c: return 'border-color:'+c
    m=re.match(r'^placeholder-(.+)$',u)  # se maneja con ::placeholder
    # borders
    if u=='border': return 'border-width:1px'
    if u=='border-0': return 'border-width:0'
    if u=='border-b': return 'border-bottom-width:1px'
    if u=='border-t': return 'border-top-width:1px'
    if u=='border-r': return 'border-right-width:1px'
    if u=='border-l': return 'border-left-width:1px'
    if u=='border-y': return 'border-top-width:1px;border-bottom-width:1px'
    if u in RADIUS: return 'border-radius:'+RADIUS[u]
    # gradients
    if u=='bg-gradient-to-r': return 'background-image:linear-gradient(to right,var(--tw-from,transparent),var(--tw-to,transparent))'
    if u=='bg-gradient-to-br': return 'background-image:linear-gradient(to bottom right,var(--tw-from,transparent),var(--tw-to,transparent))'
    if u=='bg-gradient-to-b': return 'background-image:linear-gradient(to bottom,var(--tw-from,transparent),var(--tw-to,transparent))'
    m=re.match(r'^from-(.+)$',u)
    if m:
        c=color_of(m.group(1))
        if c: return '--tw-from:'+c
    m=re.match(r'^to-(.+)$',u)
    if m:
        c=color_of(m.group(1))
        if c: return '--tw-to:'+c
    m=re.match(r'^via-(.+)$',u)
    if m:
        c=color_of(m.group(1))
        if c: return '--tw-via:'+c
    # effects
    if u in SHADOW: return 'box-shadow:'+SHADOW[u]
    if u=='shadow-none': return 'box-shadow:none'
    m=re.match(r'^opacity-(\d+)$',u)
    if m: return f'opacity:{int(m.group(1))/100}'
    if u=='overflow-hidden': return 'overflow:hidden'
    if u=='overflow-x-auto': return 'overflow-x:auto'
    if u=='overflow-y-auto': return 'overflow-y:auto'
    if u=='cursor-pointer': return 'cursor:pointer'
    if u=='backdrop-blur' or u=='backdrop-blur-sm': return 'backdrop-filter:blur(8px)'
    if u=='scroll-smooth': return 'scroll-behavior:smooth'
    if u=='ring-1': return 'box-shadow:0 0 0 1px var(--tw-ring,rgba(99,102,241,.3))'
    if u=='ring-2': return 'box-shadow:0 0 0 2px var(--tw-ring,rgba(99,102,241,.3))'
    m=re.match(r'^ring-(.+)$',u)
    if m:
        c=color_of(m.group(1))
        if c: return '--tw-ring:'+c
    # transforms / transitions
    if u=='transform': return ''
    if u=='transition' or u=='transition-transform' or u=='transition-all': return 'transition:all .2s ease'
    if u=='transition-colors': return 'transition:color .2s,background-color .2s,border-color .2s'
    m=re.match(r'^duration-(\d+)$',u)
    if m: return f'transition-duration:{m.group(1)}ms'
    if u=='ease-in-out': return 'transition-timing-function:cubic-bezier(.4,0,.2,1)'
    if u=='-translate-x-full': return 'transform:translateX(-100%)'
    if u=='translate-x-0': return 'transform:translateX(0)'
    if u=='-translate-x-1/2': return 'transform:translateX(-50%)'
    if u=='-translate-y-0.5': return 'transform:translateY(-0.125rem)'
    if u=='object-cover': return 'object-fit:cover'
    if u=='pointer-events-none': return 'pointer-events:none'
    if u=='select-none': return 'user-select:none'
    if u=='align-middle': return 'vertical-align:middle'
    return None

VARIANT_MQ={'sm':'@media (min-width:640px)','md':'@media (min-width:768px)','lg':'@media (min-width:1024px)','xl':'@media (min-width:1280px)','2xl':'@media (min-width:1536px)'}
PSEUDO={'hover':':hover','focus':':focus','active':':active','disabled':':disabled','last':':last-child','first':':first-child','odd':':nth-child(odd)','even':':nth-child(even)'}

classes=sorted(set(open('/tmp/classes.txt').read().split('\n'))-{''})
rules_base=[]; rules_mq={k:[] for k in VARIANT_MQ}; unhandled=[]; special=[]

for c in classes:
    parts=c.split(':')
    util=parts[-1]; variants=parts[:-1]
    sel='.'+esc(c)
    # pseudo selectors
    pseudo=''
    group=False; placeholder=False
    mq=None
    for v in variants:
        if v in VARIANT_MQ: mq=v
        elif v in PSEUDO: pseudo+=PSEUDO[v]
        elif v=='group-hover': group=True
        elif v=='placeholder': placeholder=True
        else: pseudo=pseudo  # ignore unknown variant
    d=decl(util)
    if d is None:
        # placeholder-color / space-y handled specially below
        unhandled.append(c); continue
    if d=='' : continue
    if group:
        full=f'.group:hover {sel}{{{d}}}'
    elif placeholder:
        full=f'{sel}::placeholder{{{d}}}'
    else:
        full=f'{sel}{pseudo}{{{d}}}'
    if mq: rules_mq[mq].append(full)
    else: rules_base.append(full)

# space-y-N / space-x-N  (selector > * + *)
for c in classes:
    m=re.match(r'^space-y-(.+)$',c)
    if m and m.group(1) in SP:
        rules_base.append(f'.{esc(c)}>:not([hidden])~:not([hidden]){{margin-top:{SP[m.group(1)]}}}')
    m=re.match(r'^space-x-(.+)$',c)
    if m and m.group(1) in SP:
        rules_base.append(f'.{esc(c)}>:not([hidden])~:not([hidden]){{margin-left:{SP[m.group(1)]}}}')
    # divide-y
    if c=='divide-y':
        rules_base.append('.divide-y>:not([hidden])~:not([hidden]){border-top-width:1px}')
    m=re.match(r'^divide-(.+)$',c)
    if m and c!='divide-y':
        col=color_of(m.group(1))
        if col: rules_base.append(f'.{esc(c)}>:not([hidden])~:not([hidden]){{border-color:{col}}}')
    # placeholder color
    m=re.match(r'^placeholder:?(.*)$',c)
    # focus:ring color & focus:border
# build output
out=[]
# reset mínimo
out.append("*,::before,::after{box-sizing:border-box;border:0 solid #e5e7eb}html{line-height:1.5;-webkit-text-size-adjust:100%}body{margin:0;line-height:inherit}h1,h2,h3,h4,p{margin:0}a{color:inherit;text-decoration:inherit}button,input,select,textarea{font:inherit;color:inherit;margin:0}button{cursor:pointer;background:none}ul{margin:0;padding:0;list-style:none}svg{display:block;vertical-align:middle}table{border-collapse:collapse}img{max-width:100%;display:block}input::placeholder,textarea::placeholder{color:#94a3b8}")
out.append('\n'.join(rules_base))
for k,mq in VARIANT_MQ.items():
    if rules_mq[k]:
        out.append(mq+'{'+'\n'.join(rules_mq[k])+'}')

css='\n'.join(out)
open('public/css/app.css','w',encoding='utf-8').write(css)
print("CSS generado:", len(css), "bytes ->", len(rules_base),"reglas base")
print("No manejadas:", len(set(unhandled)))
for u in sorted(set(unhandled))[:60]: print("  ", u)

CUSTOM = r"""
.\!translate-x-0{transform:translateX(0)!important}
.cursor-not-allowed{cursor:not-allowed}
.-top-3{top:-0.75rem}
.tracking-wide{letter-spacing:0.025em}
.text-emerald-800{color:#065f46}
.text-rose-800{color:#9f1239}
.list-disc{list-style:disc}
.list-inside{list-style-position:inside}
.last\:border-r-0:last-child{border-right-width:0}
.focus\:ring-0:focus{box-shadow:none;outline:none}
.shadow-cyan-500\/20{box-shadow:0 10px 15px -3px rgba(23,184,207,.25)}
.shadow-cyan-300\/50{box-shadow:0 4px 12px rgba(103,232,249,.5)}
[x-cloak]{display:none!important}
.sidebar-scroll::-webkit-scrollbar{width:6px}
.sidebar-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:9999px}
.hero-bg{background:radial-gradient(900px 500px at 18% 12%,rgba(34,211,238,.20),transparent 55%),radial-gradient(900px 600px at 85% 20%,rgba(99,102,241,.30),transparent 55%),linear-gradient(165deg,#091f33 0%,#0b2c46 45%,#101b3c 100%)}
.brand-bg{background:radial-gradient(700px 400px at 20% 15%,rgba(34,211,238,.25),transparent 55%),radial-gradient(700px 500px at 85% 80%,rgba(99,102,241,.40),transparent 55%),linear-gradient(165deg,#0a2236 0%,#0c2c46 45%,#101b3c 100%)}
.grad-text{background:linear-gradient(90deg,#67e8f9,#7dd3fc,#a5b4fc);-webkit-background-clip:text;background-clip:text;color:transparent}
input:focus,select:focus,textarea:focus{outline:2px solid rgba(99,102,241,.4);outline-offset:0}
"""
with open('public/css/app.css','a',encoding='utf-8') as f:
    f.write('\n'+CUSTOM)
print("CUSTOM agregado. Tamaño final:", len(open('public/css/app.css').read()), "bytes")
