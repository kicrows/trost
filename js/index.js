gsap.registerPlugin(ScrollTrigger);

// repeat first three items by cloning them and appending them to the .grid
const repeatItems = (parentEl, total = 0) => {
    const items = [...parentEl.children];
    for (let i = 0; i <= total-1; ++i) {
        var cln = items[i].cloneNode(true);
        parentEl.appendChild(cln);
    }
};

const lenis = new Lenis({
    smooth: true,
    infinite: true
});

lenis.on('scroll',()=>{
  ScrollTrigger.update()
})

function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}

imagesLoaded( document.querySelectorAll('.grid__item'), { background: true }, () => {
    // Toggle preserveAspectRatio for desktop vs mobile to avoid desktop slicing
    // Keep desktop as 'meet' via markup; only force 'none' on small screens
    const logoSvg = document.querySelector('.grid__item-logo');
    const syncLogoPAR = () => {
        if (!logoSvg) return;
        const isDesktop = window.matchMedia('(min-width: 53em)').matches;
        if (isDesktop) {
            logoSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
        } else {
            logoSvg.setAttribute('preserveAspectRatio', 'none');
        }
    };
    syncLogoPAR();
    window.addEventListener('resize', syncLogoPAR);


    document.body.classList.remove('loading');

    repeatItems(document.querySelector('.grid'), 1);

    const items = [...document.querySelectorAll('.grid__item')];

    // first item
    const firtsItem = items[0];
    gsap.set(firtsItem, {transformOrigin: '50% 100%', opacity: 1})
    gsap.fromTo(firtsItem,
        { scaleY: 1 },
        {
        ease: 'none',
        scaleY: 0.0001,
        immediateRender: false,
        scrollTrigger: {
            trigger: firtsItem,
            start: 'center center',
            end: 'bottom top',
            scrub: true,
            fastScrollEnd: true,
            onUpdate: self => {
                const p = self.progress;
                const opacity = p > 0.9 ? 1 - (p - 0.9) / 0.1 : 1;
                gsap.set(firtsItem, { opacity });
            },
            onLeave: () => {
                // Don't reset scale - let it stay at current value
            },
            invalidateOnRefresh: true,
        }
    });

    // last item  
    const lastItem = items[2];
    gsap.set(lastItem, {transformOrigin: '50% 0%', scaleY: 0, opacity: 0})
    gsap.fromTo(lastItem,
        { scaleY: 0.0001 },
        {
        ease: 'none',
        scaleY: 1,
        immediateRender: false,
        scrollTrigger: {
            trigger: lastItem,
            start: 'top bottom',
            end: 'bottom top',
            scrub: true,
            fastScrollEnd: true,
            onUpdate: self => {
                const p = self.progress;
                const opacity = p < 0.1 ? p / 0.1 : 1;
                gsap.set(lastItem, { opacity });
            },
            onLeaveBack: () => {
                // Don't reset scale - let it stay at current value
            },
            invalidateOnRefresh: true,
        }
    });
    
    // in between
    let ft;
    let st;
    const middleItem = items[1];
        
    ft = gsap.timeline({ defaults: { immediateRender: false } })
    .fromTo(middleItem, { scale: 0.0001 }, {
        ease: 'none',
        onStart: () => {
            if (st) st.kill()
        },
        scale: 1,
        scrollTrigger: {
            trigger: middleItem,
            start: 'top bottom',
            end: 'center center',
            scrub: true,
            onEnter: () => gsap.set(middleItem, {transformOrigin: '50% 0%'}),
            onEnterBack: () => gsap.set(middleItem, {transformOrigin: '50% 0%'}),
            onLeave: () => gsap.set(middleItem, {transformOrigin: '50% 100%'}),
            onLeaveBack: () => gsap.set(middleItem, {transformOrigin: '50% 100%'}),
            onUpdate: self => {
                const p = self.progress;
                const opacity = p < 0.1 ? p / 0.1 : 1;
                gsap.set(middleItem, { opacity });
            },
            invalidateOnRefresh: true,
        },
    });

    st = gsap.timeline({ defaults: { immediateRender: false } })
    .fromTo(middleItem, { scale: 1 }, {
        ease: 'none',
        onStart: () => {
            if (ft) ft.kill()
        },
        scale: 0.0001,
        scrollTrigger: {
            trigger: middleItem,
            start: 'center center',
            end: 'bottom top',
            scrub: true,
            onEnter: () => gsap.set(middleItem, {transformOrigin: '50% 100%'}),
            onEnterBack: () => gsap.set(middleItem, {transformOrigin: '50% 100%'}),
            onLeave: () => gsap.set(middleItem, {transformOrigin: '50% 0%'}),
            onLeaveBack: () => gsap.set(middleItem, {transformOrigin: '50% 0%'}),
            onUpdate: self => {
                const p = self.progress;
                const opacity = p > 0.9 ? 1 - (p - 0.9) / 0.1 : 1;
                gsap.set(middleItem, { opacity });
            },
            invalidateOnRefresh: true,
        },
    });
    
    requestAnimationFrame(raf);
    
    const refresh = () => {
        ScrollTrigger.clearScrollMemory();
        window.history.scrollRestoration = 'manual';
        ScrollTrigger.refresh(true);
    }

    refresh();
    window.addEventListener('resize', refresh);

});