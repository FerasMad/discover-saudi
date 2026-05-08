</main>

<footer class="site-footer">
  <div class="container site-footer-inner">
    <p class="site-footer-meta">
      © <?= date('Y') ?> اكتشف السعودية — مشروع جامعة الملك سعود.
      <span class="site-footer-by">صنع بواسطة فراس مدخلي وتميم العصيمي.</span>
    </p>
    <ul class="site-footer-links">
      <li><a href="<?= url('gallery.php') ?>">معرض المناطق</a></li>
      <li><a href="<?= url('admin/login.php') ?>">دخول المشرف</a></li>
      <li>
        <a class="site-footer-gh" href="https://github.com/FerasMad/discover-saudi" target="_blank" rel="noopener" aria-label="المصدر على GitHub">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
            <path d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.11.79-.25.79-.56v-2c-3.2.7-3.88-1.36-3.88-1.36-.52-1.33-1.27-1.69-1.27-1.69-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.18 1.76 1.18 1.03 1.76 2.69 1.25 3.35.96.1-.74.4-1.25.73-1.54-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.29 1.18-3.1-.12-.29-.51-1.46.11-3.04 0 0 .96-.31 3.15 1.18.91-.25 1.89-.38 2.86-.38.97 0 1.95.13 2.86.38 2.19-1.49 3.15-1.18 3.15-1.18.62 1.58.23 2.75.11 3.04.74.81 1.18 1.84 1.18 3.1 0 4.42-2.69 5.39-5.25 5.68.41.36.78 1.05.78 2.12v3.14c0 .31.21.68.8.56 4.56-1.52 7.85-5.83 7.85-10.91C23.5 5.65 18.35.5 12 .5z"/>
          </svg>
          <span>GitHub</span>
        </a>
      </li>
    </ul>
  </div>
</footer>

<script src="<?= asset('js/theme.js') ?>" defer></script>
<?php if (!empty($page_scripts) && is_array($page_scripts)): foreach ($page_scripts as $src): ?>
<script src="<?= asset($src) ?>" defer></script>
<?php endforeach; endif; ?>

</body>
</html>
