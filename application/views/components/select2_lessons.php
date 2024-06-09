<select name="<?php echo $options['input_name']; ?>" id="<?php echo $options['id']; ?>" class="custom-select select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" <?php echo @$options['required'] ? 'required' : ''; ?> <?php echo array_key_exists('multiple', $options) ? 'multiple="multiple"' : ''; ?>>
    <?php if(!array_key_exists('multiple',$options)): ?> 
        <option value="" selected disabled><?php echo (array_key_exists('placeholder', $options)) ? $options["placeholder"] : 'Vyberte lekci'; ?></option>
    <?php endif; ?>
    <?php foreach($data as $item): ?>
        <?php if(array_key_exists('selected',$options) && is_array($options['selected'])): // Multiple selected items in array ?> 
            <option value="<?php echo $item['id']; ?>" <?php echo ( in_array($item['id'],$options['selected']) ) ? 'selected' : ''; ?>><?php echo $item['name']; ?></option>  
        <?php else: // just one selected item ?>    
            <option value="<?php echo $item['id']; ?>" <?php echo ( (array_key_exists('selected',$options)) && ($options['selected'] == $item['id']) ) ? 'selected' : ''; ?>><?php echo $item['name']; ?></option>     
        <?php endif; ?>       
    <?php endforeach; ?>        
</select>