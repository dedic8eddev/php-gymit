<select name="<?php echo $options['input_name']; ?>" id="<?php echo $options['id']; ?>" class="form-control" tabindex="-1" aria-hidden="true" <?php echo array_key_exists('required',$options) ? 'required' : ''; ?> <?php echo array_key_exists('multiple', $options) ? 'multiple="multiple"' : ''; ?>>
    <?php if(empty(@$options['selected'])): ?>
        <option value="" selected disabled>Vyberte typ</option>
    <?php endif; ?>
    <?php foreach($data as $k => $v): ?>
        <option value="<?php echo $k; ?>" <?php echo ( (array_key_exists('selected',$options)) && ($options['selected'] == $k) ) ? 'selected' : ''; ?>><?php echo $v; ?></option>
    <?php endforeach; ?>
</select>