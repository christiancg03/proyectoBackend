<footer style="background-color: #1a1d2e; color: #00f0ff; border-top: 1px solid #292c3e;">
  <div class="container py-3">
    <div class="row align-items-center">
      <div class="col-md-4 mb-2 mb-md-0 d-flex align-items-center gap-2">
        <i class="fa-solid fa-gamepad"></i>
        <span style="font-weight: bold; letter-spacing: 1px;">Game Nation</span>
        <span class="text-muted small ms-2">&copy; <?= date('Y') ?></span>
      </div>
      <div class="col-md-5 mb-2 mb-md-0 links-footer">
        <a href="/GameNation/Usuarios/views/user/amigos.php" class="footer-link me-3">Chat</a>
        <a href="/GameNation/Usuarios/views/user/noticias.php" class="footer-link me-3">Noticias</a>
        <a href="/GameNation/Usuarios/views/user/biblioteca.php" class="footer-link me-3">Biblioteca</a>
        <a href="/GameNation/Usuarios/views/user/contacto.php" class="footer-link">Contacto</a>
      </div>
      <div class="col-md-3 text-md-end">
        <a href="https://twitter.com" target="_blank" class="footer-icon me-2"><i class="fab fa-twitter"></i></a>
        <a href="https://instagram.com" target="_blank" class="footer-icon me-2"><i class="fab fa-instagram"></i></a>
        <a href="https://discord.com" target="_blank" class="footer-icon"><i class="fab fa-discord"></i></a>
      </div>
    </div>
    <div class="row mt-2">
      <div class="col text-center">
        <a href="/GameNation/Usuarios/views/user/aviso_legal.php" class="footer-link small me-2">Aviso Legal</a>
        <a href="/GameNation/Usuarios/views/user/politica_privacidad.php" class="footer-link small">Política de Privacidad</a>
      </div>
    </div>
  </div>
</footer>

<style>
  html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
body {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
main {
  flex: 1 0 auto;
}
footer {
  flex-shrink: 0;
  margin-top: 3%;
}
.footer-link {
  color: #00f0ff;
  text-decoration: none;
  transition: color 0.2s;
}
.footer-link:hover {
  color: #7ae4ff;
  text-decoration: underline;
}
.footer-icon {
  color: #00f0ff;
  font-size: 1.2rem;
  transition: color 0.2s, transform 0.2s;
}
.footer-icon:hover {
  color: #7ae4ff;
  transform: scale(1.15);
}
.links-footer {
  padding-right: 7rem; /* Ajusta el valor a tu gusto */
}
@media (max-width: 700px) {
  .footer .row > [class^="col-"] {
    text-align: center !important;
    justify-content: center !important;
    align-items: center !important;
    margin-bottom: 0.7rem;
  }
  .links-footer {
    padding-right: 0 !important;
    padding-left: 0 !important;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: center;
  }
  .footer-icon {
    margin-bottom: 0.5rem;
  }
  .col-md-4.d-flex.align-items-center {
    justify-content: center !important;
    text-align: center !important;
    width: 100%;
  }
}
</style>