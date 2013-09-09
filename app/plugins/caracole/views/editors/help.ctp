<?php
/**
 *	Help popup
 **/
if (!empty($data['tinyMCE'])) {
	echo $this->Fastcode->p(
		sprintf(
			__d('caracole', 'The RTE (Rich Text Editor) you are using is tinyMCE %1$s, release by Moxiecode Systems AB under a LGPL license.', true),
			$data['tinyMCE']['majorVersion'].'.'.$data['tinyMCE']['majorVersion']
		)
	);
}

echo $this->Fastcode->p(
	__d('caracole', 'The grey boxes around your text represent paragraphs, they will not appear on your website once published, but are here to help you better spot the various parts of your text.', true)
);

echo $this->Fastcode->p(
	__d('caracole', 'By pressing Enter, you create a new paragraph. Pressing Ctrl+Enter will create a new line but stay in the same paragraph.', true)
);

echo $this->Fastcode->p(
	__d('caracole', 'You can undo/redo you last actions by using the Ctrl+Z / Ctrl+Y shortcuts', true)
);

echo $this->Fastcode->p(
	__d('caracole', 'You can open a secondary toolbar, with more options, by pressing the double arrows on the right side of the toolbar.', true)
);

echo $this->Fastcode->p(
	__d('caracole', 'If you have already written your text under Microsoft Word, you have to use the "Paste from Word" feature, otherwise your text will not be rendered correctly.', true)
);
