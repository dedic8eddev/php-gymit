<select name="<?php echo $options['input_name']; ?>" id="<?php echo @$options['id']; ?>" class="<?php echo array_key_exists('init_later',$options) ? '':'custom-select select2 select2-hidden-accessible'; ?>" tabindex="-1" aria-hidden="true" <?php echo array_key_exists('required',$options) ? 'required' : ''; ?> <?php echo array_key_exists('multiple', $options) ? 'multiple="multiple"' : ''; ?>>

        <?php if(isset($options["empty"])): ?><option disabled selected>Vyberte účet..</option><?php endif; ?>

        <?php foreach($data as $key => $item): ?>
            <option value="<?php echo $key; ?>" <?php echo ( (array_key_exists('selected',$options)) && ($options['selected'] == $key OR in_array($key, $options["selected"])) ) ? 'selected' : ''; ?>><?php echo $item['name'] . ' ('.$item["value"].')'; ?></option>
        <?php endforeach; ?>
</select>