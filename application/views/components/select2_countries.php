<select name="<?php echo $options['input_name']; ?>" id="<?php echo $options['id']; ?>" class="custom-select select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" <?php echo array_key_exists('required',$options) ? 'required' : ''; ?>>
    <option value="" selected disabled>Vyberte zemi</option>
        <?php if(empty(@$options['selected'])) $options['selected']='CZ'; ?>
        <?php foreach($data as $item): ?>
            <option value="<?php echo $item['iso']; ?>" <?php echo ( (array_key_exists('selected',$options)) && ($options['selected'] == $item['iso']) ) ? 'selected' : ''; ?>><?php echo $item['name']; ?></option>
        <?php endforeach; ?>
</select>