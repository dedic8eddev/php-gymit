<?php if(!empty($custom_fields)): ?>
    <?php foreach($custom_fields as $field): ?>
        <?php if($field->type == 'text'): ?>
            <div class="form-group">
                <label for="customfield_<?php echo $field->id; ?>"><?php echo $field->name; ?><?php if($field->required){ echo ' <span class="required">*</span>'; } ?></label>
                <input <?php if($field->required){ echo 'required'; } ?> class="form-control" type="text" name="<?php echo 'customfield_' . $field->id; ?>" placeholder="<?php echo $field->name; ?>" <?php if($values){foreach($values as $v){ if($v->field_id == $field->id){ echo 'value="'.$v->value.'"'; } }}; ?>>
            </div>
        <?php elseif($field->type == 'number'): ?>
            <div class="form-group">
                <label for="customfield_<?php echo $field->id; ?>"><?php echo $field->name; ?><?php if($field->required){ echo ' <span class="required">*</span>'; } ?></label>
                <input <?php if($field->required){ echo 'required'; } ?> class="form-control" type="number" name="<?php echo 'customfield_' . $field->id; ?>" placeholder="<?php echo $field->name; ?>" <?php if($values){foreach($values as $v){ if($v->field_id == $field->id){ echo 'value="'.$v->value.'"'; } }}; ?>>
            </div>
        <?php elseif($field->type == 'select'): ?>
            <div class="form-group">
                <label for="customfield_<?php echo $field->id; ?>"><?php echo $field->name; ?><?php if($field->required){ echo ' <span class="required">*</span>'; } ?></label>
                <select <?php if($field->required){ echo 'required'; } ?> name="<?php echo 'customfield_' . $field->id; ?>" class="form-control">
                    <?php foreach(json_decode($field->type_params) as $i => $param): ?>
                        <option <?php if($values){foreach($values as $v){ if($v->field_id == $field->id && $v->value == $i){ echo 'selected'; } }}; ?> value="<?php echo $i; ?>"><?php echo $param; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>