


<footer>


<?php
    $adress = get_field("adress", "option");
    ?>

    <div>
        <h1>Adresse></h1>
        <p><?php echo esc_html($adress); ?></p>    
    </div>



</footer>
<?php wp_footer(); ?>
</body>
</html>