    <!-- Scripts -->
    <script>
        // Inject PHP data to JS
        const serverClassData = <?php 
            $jsClasses = [];
            foreach($classItems as $c) {
                $jsClasses[$c['name']] = [
                    'title' => $c['name'],
                    'desc' => $c['description'],
                    'image' => $c['image'],
                    'features' => json_decode($c['features'], true) ?: []
                ];
            }
            echo json_encode($jsClasses);
        ?>;
    </script>
