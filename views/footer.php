            </main>
            <footer>
                Queries: <?php echo RuntimeStats::getTotalQueries() ?>,
                Time: <?php echo number_format(RuntimeStats::getTimer(), 4, '.', '') ?> seconds
            </footer>
        </div>
    </body>
</html>
