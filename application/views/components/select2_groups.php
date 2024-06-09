<select name="<?php echo $options['input_name']; ?>" id="<?php echo $options['id']; ?>" class="custom-select select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" <?php echo array_key_exists('required',$options) ? 'required' : ''; ?> <?php echo array_key_exists('multiple', $options) ? 'multiple="multiple"' : ''; ?>>
        <?php if(isset($options["empty"])): ?><option disabled selected>Vyberte skupinu..</option><?php endif; ?>

        <?php foreach($data as $group): ?>
            <option value="<?php echo $group->id; ?>" <?php echo ( (array_key_exists('selected',$options)) && ($options['selected'] == $group->id) ) ? 'selected' : ''; ?> ><?php echo $group->description; ?></option>
        <?php endforeach; ?>
</select>