<p class="centered"
   data-behavior="reloadIframe modalAutoClose"
   data-target="email-preview"
   <?php echo (isset($layout_repeated_id) && $layout_repeated_id ? 'data-url-add="/editing_id/'.$model->id.'"' : ''); ?>
>
  <?php echo $message; ?>
</p>
