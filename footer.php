    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-info">
                <div class="footer-contacts">
                    <p><a href="">example@gmail.com</a></p>
                    <p>+7 (123) 456-78-90</p>
                </div>
            </div>

            <div class="footer-social">
                <a href="" target="_blank" rel="noopener noreferrer" class="social-link max">Max</a>
                <a href="" target="_blank" rel="noopener noreferrer" class="social-link vk">ВКонтакте</a>
                <a href="" class="social-link email">Email</a>
            </div>

            <div class="footer-copyright">
                <p>
                    © <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Все права защищены.<br>
                    <a href="/privacy-policy" target="_blank">Политика конфиденциальности</a>
                </p>
            </div>
        </div>
    </footer>

        <!-- Контейнер для всплывающих уведомлений -->
    <div id="notification-toast" class="notification-toast"></div>
    
    <?php wp_footer(); ?>
</body>
</html>