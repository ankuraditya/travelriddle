(() => {
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  if (toggle && nav) {
    const close = () => { nav.classList.remove('is-open'); document.body.classList.remove('menu-open'); toggle.setAttribute('aria-expanded', 'false'); };
    toggle.addEventListener('click', () => {
      const open = !nav.classList.contains('is-open');
      nav.classList.toggle('is-open', open);
      document.body.classList.toggle('menu-open', open);
      toggle.setAttribute('aria-expanded', String(open));
    });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') close(); });
    nav.addEventListener('click', event => { if (event.target.closest('a')) close(); });
  }

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const videos = [...document.querySelectorAll('.hero__video')];
  let videoIndex = 0;
  let videoTimer;
  const showVideo = index => {
    videoIndex = (index + videos.length) % videos.length;
    videos.forEach((video, i) => {
      const active = i === videoIndex;
      video.classList.toggle('is-active', active);
      if (active) {
        video.currentTime = 0;
        video.play().catch(() => {});
      } else {
        video.pause();
      }
    });
  };
  if (videos.length) {
    const setHeroRatio = video => {
      if (video.videoWidth && video.videoHeight) {
        document.querySelector('.hero--editorial')?.style.setProperty('--travel-video-ratio', `${video.videoWidth} / ${video.videoHeight}`);
      }
    };
    videos.forEach(video => video.addEventListener('loadedmetadata', () => setHeroRatio(video), { once: true }));
    if (videos[0].readyState >= 1) setHeroRatio(videos[0]);
    const restartVideos = () => { clearInterval(videoTimer); if (!reducedMotion) videoTimer = setInterval(() => showVideo(videoIndex + 1), 8000); };
    showVideo(0);
    restartVideos();
  }

  const quotes = [...document.querySelectorAll('.quote-slide')];
  const currentQuote = document.querySelector('[data-quote-current]');
  let quoteIndex = 0;
  let quoteTimer;
  const showQuote = index => {
    quoteIndex = (index + quotes.length) % quotes.length;
    quotes.forEach((quote, i) => quote.classList.toggle('is-active', i === quoteIndex));
    if (currentQuote) currentQuote.textContent = String(quoteIndex + 1).padStart(2, '0');
  };
  const restartQuotes = () => { clearInterval(quoteTimer); if (!reducedMotion) quoteTimer = setInterval(() => showQuote(quoteIndex + 1), 7000); };
  if (quotes.length) {
    document.querySelector('[data-quote-prev]')?.addEventListener('click', () => { showQuote(quoteIndex - 1); restartQuotes(); });
    document.querySelector('[data-quote-next]')?.addEventListener('click', () => { showQuote(quoteIndex + 1); restartQuotes(); });
    restartQuotes();
  }
})();
