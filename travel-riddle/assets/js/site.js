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
  let videoPaused = reducedMotion;
  let soundEnabled = false;
  const pauseButton = document.querySelector('[data-video-pause]');
  const soundButton = document.querySelector('[data-video-sound]');
  const setHeroRatio = video => {
    if (video.videoWidth && video.videoHeight) document.querySelector('.hero--editorial')?.style.setProperty('--travel-video-ratio', `${video.videoWidth} / ${video.videoHeight}`);
  };
  const showVideo = index => {
    clearTimeout(videoTimer);
    videoIndex = (index + videos.length) % videos.length;
    videos.forEach((video, i) => {
      const active = i === videoIndex;
      video.classList.toggle('is-active', active);
      if (active) {
        video.currentTime = 0;
        video.muted = !soundEnabled || video.dataset.hasAudio !== 'true';
        setHeroRatio(video);
        if (!videoPaused) video.play().catch(() => { if (!video.muted) { soundEnabled = false; video.muted = true; video.play().catch(() => {}); } });
      } else {
        video.pause();
      }
    });
    if (soundButton) {
      soundButton.hidden = videos[videoIndex].dataset.hasAudio !== 'true';
      soundButton.textContent = soundEnabled ? 'Sound off' : 'Sound on';
      soundButton.setAttribute('aria-pressed', String(soundEnabled));
    }
    if (!videoPaused) videoTimer = setTimeout(() => showVideo(videoIndex + 1), 20000);
  };
  if (videos.length) {
    videos.forEach((video, index) => {
      video.addEventListener('loadedmetadata', () => { if (index === videoIndex) setHeroRatio(video); });
      video.addEventListener('ended', () => { if (index === videoIndex && !videoPaused) showVideo(videoIndex + 1); });
      video.addEventListener('error', () => { if (index === videoIndex && !videoPaused) showVideo(videoIndex + 1); });
    });
    pauseButton?.addEventListener('click', () => {
      videoPaused = !videoPaused;
      clearTimeout(videoTimer);
      pauseButton.textContent = videoPaused ? 'Play' : 'Pause';
      pauseButton.setAttribute('aria-label', videoPaused ? 'Play banner videos' : 'Pause banner videos');
      if (videoPaused) videos[videoIndex].pause();
      else { videos[videoIndex].play().catch(() => {}); videoTimer = setTimeout(() => showVideo(videoIndex + 1), 20000); }
    });
    document.querySelector('[data-video-next]')?.addEventListener('click', () => showVideo(videoIndex + 1));
    soundButton?.addEventListener('click', () => {
      soundEnabled = !soundEnabled;
      videos[videoIndex].muted = !soundEnabled;
      soundButton.textContent = soundEnabled ? 'Sound off' : 'Sound on';
      soundButton.setAttribute('aria-pressed', String(soundEnabled));
    });
    if (reducedMotion && pauseButton) { pauseButton.textContent = 'Play'; pauseButton.setAttribute('aria-label', 'Play banner videos'); }
    showVideo(0);
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
