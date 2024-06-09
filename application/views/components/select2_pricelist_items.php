<select name="<?php echo $options['input_name']; ?>" id="<?php echo $options['id']; ?>" class="custom-select select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" <?php echo array_key_exists('required',$options) ? 'required' : ''; ?> <?php echo array_key_exists('multiple', $options) ? 'multiple="multiple"' : ''; ?>>
        <?php foreach($data as $item): ?>
            <option value="<?php echo $item['id']; ?>" data-duration="<?php echo $item['duration']; ?>" <?php echo ( (array_key_exists('selected',$options)) && ($options['selected'] == $item['id'] OR in_array($item["id"], $options["selected"])) ) ? 'selected' : ''; ?>><?php echo $item['name']; ?></option>
        <?php endforeach; ?>
</select>