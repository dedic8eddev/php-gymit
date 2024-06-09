<select placeholder="Čtečka.." name="<?php echo $options['input_name']; ?>" id="<?php echo $options['id']; ?>" class="<?php echo array_key_exists('default-form-class', $options) ? 'form-control' : 'bg-transparent border-0'; ?>" <?php echo array_key_exists('required',$options) ? 'required' : ''; ?> <?php echo array_key_exists('multiple', $options) ? 'multiple="multiple"' : ''; ?>>
        <?php foreach($data as $item): ?>
            <option value="<?php echo $item->reader_id; ?>" <?php echo ( (array_key_exists('selected',$options)) && ($options['selected'] == $item->reader_id OR in_array($item->reader_id, $options["selected"])) ) ? 'selected' : ''; ?>><?php echo $item->name; ?></option>
        <?php endforeach; ?>
</select>