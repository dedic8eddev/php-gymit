<select name="<?php echo $options['input_name']; ?>" id="<?php echo $options['id']; ?>" class="custom-select select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" <?php echo array_key_exists('required',$options) ? 'required' : ''; ?> <?php echo array_key_exists('multiple', $options) ? 'multiple="multiple"' : ''; ?>>
        <?php if(isset($options["empty"])): ?><option disabled selected>Vyberte uživatele..</option><?php endif; ?>

        <?php foreach($data as $group_name => $users): ?>
            <optgroup label="<?php echo $group_name; ?>">
                <?php foreach($users as $item): ?>
                    <option value="<?php echo (isset($options["onlyCards"])) ? $item["card_id"] : $item['id']; ?>" <?php echo ( (array_key_exists('selected',$options)) && ($options['selected'] == $item['id'] OR in_array($item["id"], $options["selected"])) ) ? 'selected' : ''; ?>><?php echo $item['first_name'] . " " . $item["last_name"] . " (".$item["email"].")"; ?></option>
                <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
</select>