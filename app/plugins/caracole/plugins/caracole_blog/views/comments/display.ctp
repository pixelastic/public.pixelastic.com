<?php
	/**
	 *	comment display
	 *	Displaying one comment
	 **/
	// List of classes to apply to the comment
	$commentClass = array('commentDisplay', 'wrapper');
	if (!empty($item[Configure::read('Auth.modelAlias')]['is_admin'])) $commentClass[] = 'commentAdmin';
	if (!empty($item['is_spam'])) $commentClass[] = 'commentSpam';

	// Wrapping in a div
	echo $this->Html->tag('div', null, array(
		'id' => 'comment'.$item['id'],
		'class' =>  implode(' ', $commentClass)
	));
	?>
	<div class="commentMain">
		<?php
			// Display comments by forbidding HTML
			echo $this->Fastcode->div($this->Fastcode->html($item['text']), 'text');

			// Infos
			$item['author'] = $this->Fastcode->html($item['author']);
			$author = empty($item['website']) ? $item['author'] : $this->Fastcode->link($item['author'], $item['website'], array('target' => '_blank'));
			$date = $this->Time->timeAgoInWords(strtotime($item['created']));

			echo $this->Html->tag('div', null, array('class' => 'infos'));
				// Author
				echo $this->Html->tag('span', $author, array('class' => 'author'));
				// Date
				echo $this->Html->tag('span', $date, array('class' => 'date'));

				// Admin actions for user loggued as admins
				if (!empty($activeUser['User']['is_admin']) && is_numeric($item['id'])) {
					echo $this->Html->tag('ul', null, array('class' => 'adminActions inline'));
						// Flag/Unflag as spam
						echo $this->Html->tag('li', $this->Fastcode->link(
							empty($item['is_spam']) ? __d('caracole_blog', 'Flag as spam', true) : __d('caracole_blog', 'Remove flag spam', true),
							array('plugin' => 'caracole_blog', 'controller' => 'comments', 'admin' => true, 'action' => 'spam', 'id' => $item['id']),
							array('class' => 'commentSpam', 'icon' => empty($item['is_spam']) ? 'Comment_flag' : 'Comment_unflag')
						));
						// Delete comment
						echo $this->Html->tag('li', $this->Fastcode->link(
							__d('caracole_blog', 'Delete', true),
							array('plugin' => 'caracole_blog', 'controller' => 'comments', 'admin' => true, 'action' => 'delete', 'id' => $item['id']),
							array('icon' => 'Comment_delete', 'class' => 'commentDelete')
						));
						// Edit comment
						echo $this->Html->tag('li', $this->Fastcode->link(
							__d('caracole_blog', 'Edit', true),
							array('plugin' => 'caracole_blog', 'controller' => 'comments', 'admin' => true, 'action' => 'edit', 'id' => $item['id']),
							array('icon' => 'Comment_edit', 'target' => '_blank')
						));

					echo '</ul>';
				};
			echo '</div>'
		?>
	</div>
	<div class="commentSide">
		<div class="userAvatar">
			<?php
				echo $this->Html->image(
					'http://www.gravatar.com/avatar/'.md5($item['email']).'?d=identicon&s=50',
					array(
						'alt' => $item['author']
					)
				);
			?>
		</div>
	</div>
</div>