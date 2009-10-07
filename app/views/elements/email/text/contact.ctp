You have received a new message at:
<?php echo Router::url(array('controller' => 'contacts', 'action' => 'view', $contact['Contact']['alias']), true); ?>


Name: <?php echo $message['Message']['name']; ?>

Email: <?php echo $message['Message']['email']; ?>

Subject: <?php echo $message['Message']['title']; ?>

Message: <?php echo $message['Message']['body']; ?> 