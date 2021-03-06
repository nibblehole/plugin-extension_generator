
	<?php
    // Disable CSRF for this form
    $this->Form->setCsrfOptions(['set_on_create' => false]);
    $this->Form->create($this->Html->ifSet($post_to), ['method' => $this->Html->ifSet($form_method, 'post')]);
    if ($this->Html->ifSet($fields)) {
        foreach ($fields as $key => $value) {
            $this->Form->fieldHidden($key, $value);
        }
    }

    $this->Form->fieldImage(
        'submit',
        $this->_('{{class_name}}.buildprocess.submit', true),
        [
            'src' => '',
            'alt' => $this->_('{{class_name}}.buildprocess.submit', true),
            'title' => $this->_('{{class_name}}.buildprocess.submit', true)
        ]
    );

    $this->Form->end();
