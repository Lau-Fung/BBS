<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}"
      dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>Excel Management</title>

    {{-- Fonts (optional) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Bootstrap CSS (RTL when Arabic) --}}
    @if(app()->getLocale()==='ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @endif

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    {{-- Your own assets if any --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        .hscroll-wrapper{position:relative}
        /* Arrows follow your pointer vertically inside the scroll area */
        .hscroll-arrow{position:absolute;top:50%;transform:translateY(-50%);z-index:5;background:rgba(0,0,0,.5);color:#fff;border:none;border-radius:999px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;opacity:.75;transition:opacity .2s}
        .hscroll-arrow svg{width:18px;height:18px}
        .hscroll-wrapper:hover .hscroll-arrow{opacity:1}
        .hscroll-left{left:6px}
        .hscroll-right{right:6px}
        .hscroll-gradient-left,.hscroll-gradient-right{position:absolute;top:0;bottom:0;width:24px;pointer-events:none}
        .hscroll-gradient-left{left:0;background:linear-gradient(90deg, rgba(255,255,255,1) 20%, rgba(255,255,255,0) 100%)}
        .hscroll-gradient-right{right:0;background:linear-gradient(270deg, rgba(255,255,255,1) 20%, rgba(255,255,255,0) 100%)}
    </style>
</head>
<body>

    {{-- Top navigation --}}
    @include('layouts.navigation')

    {{-- Optional page header (slot) --}}
    @isset($header)
        <header class="bg-white border-bottom shadow-sm">
            <div class="container py-3">
                {!! $header !!}
            </div>
        </header>
    @endisset

    {{-- Page content --}}
    <main class="container py-4">
        {{ $slot }}
    </main>

    {{-- jQuery first --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    {{-- Bootstrap JS bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script>
        // Enhance all horizontal scroll containers with drag-to-scroll and arrows
        (function(){
            function enhance(el){
                if(el.classList.contains('hscroll-enhanced')) return;
                el.classList.add('hscroll-enhanced');
                const wrapper=document.createElement('div');
                wrapper.className='hscroll-wrapper';
                el.parentNode.insertBefore(wrapper, el);
                wrapper.appendChild(el);

                const left=document.createElement('button');
                left.type='button';
                left.className='hscroll-arrow hscroll-left';
                left.innerHTML='\n<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>';
                const right=document.createElement('button');
                right.type='button';
                right.className='hscroll-arrow hscroll-right';
                right.innerHTML='\n<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>';
                const gradL=document.createElement('div'); gradL.className='hscroll-gradient-left';
                const gradR=document.createElement('div'); gradR.className='hscroll-gradient-right';
                wrapper.appendChild(gradL); wrapper.appendChild(gradR);
                wrapper.appendChild(left); wrapper.appendChild(right);

                const step=200;
                left.addEventListener('click', ()=> el.scrollBy({left: -step, behavior: 'smooth'}));
                right.addEventListener('click', ()=> el.scrollBy({left: step, behavior: 'smooth'}));

                // drag to scroll
                let isDown=false,startX,scrollLeft;
                el.addEventListener('mousedown', (e)=>{isDown=true;el.classList.add('dragging');startX=e.pageX-el.offsetLeft;scrollLeft=el.scrollLeft;});
                el.addEventListener('mouseleave', ()=>{isDown=false;el.classList.remove('dragging')});
                el.addEventListener('mouseup', ()=>{isDown=false;el.classList.remove('dragging')});
                el.addEventListener('mousemove', (e)=>{if(!isDown) return;e.preventDefault();const x=e.pageX-el.offsetLeft;const walk=(x-startX);el.scrollLeft=scrollLeft-walk;});

                // follow cursor vertically within the wrapper
                wrapper.addEventListener('mousemove', (e)=>{
                    const rect = wrapper.getBoundingClientRect();
                    const y = Math.min(Math.max(e.clientY - rect.top, 17), rect.height - 17);
                    left.style.top = y + 'px';
                    right.style.top = y + 'px';
                });

                // show/hide arrows based on overflow
                function refresh(){
                    const canScroll = el.scrollWidth > el.clientWidth + 5;
                    left.style.display = right.style.display = canScroll ? 'flex' : 'none';
                    gradL.style.display = gradR.style.display = canScroll ? 'block' : 'none';
                }
                const ro = new ResizeObserver(refresh); ro.observe(el);
                refresh();
            }
            function init(){
                document.querySelectorAll('.overflow-x-auto, .table-responsive').forEach(enhance);
            }
            if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
        })();
    </script>
</body>
</html>
