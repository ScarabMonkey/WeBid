<?php
/***************************************************************************
 *   copyright				: (C) 2008 - 2014 WeBid
 *   site					: http://www.webidsupport.com/
 ***************************************************************************/

/***************************************************************************
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version. Although none of the code may be
 *   sold. If you have been sold this script, get a refund.
 ***************************************************************************/

include 'common.php';

$cat = (isset($_GET['cat'])) ? intval($_GET['cat']) : intval($_POST['cat']);
if ($cat > 0)
{
	// Retrieve category's name
	$query = "SELECT category FROM " . $DBPrefix . "faqscategories WHERE id = :cats";
	$params = array();
	$params[] = array(':cats', $cat, 'int');
	$db->query($query, $params);
	$FAQ_ctitle = $db->result('category');

	$template->assign_vars(array(
			'DOCDIR' => $DOCDIR, // Set document direction (set in includes/messages.XX.inc.php) ltr/rtl
			'PAGE_TITLE' => $system->SETTINGS['sitename'] . ' ' . $MSG['5236'] . ' - ' . $FAQ_ctitle,
			'CHARSET' => $CHARSET,
			'LOGO' => ($system->SETTINGS['logo']) ? '<a href="' . $system->SETTINGS['siteurl'] . 'index.php?"><img src="' . $system->SETTINGS['siteurl'] . 'themes/' . $system->SETTINGS['theme'] . '/' . $system->SETTINGS['logo'] . '" border="0" alt="' . $system->SETTINGS['sitename'] . '"></a>' : "&nbsp;",
			'SITEURL' => $system->SETTINGS['siteurl'],

			'FNAME' => $FAQ_ctitle
			));

	// Retrieve FAQs categories from the database
	$query = "SELECT * FROM " . $DBPrefix . "faqscategories ORDER BY category ASC";
	$db->direct_query($query);
	while ($cats = $db->result())
	{
		$template->assign_block_vars('cats', array(
				'CAT' => $cats['category'],
				'ID' => $cats['id']
				));
	}

	// Retrieve FAQs from the database
	$query = "SELECT f.question As q, f.answer As a, t.* FROM " . $DBPrefix . "faqs f
			LEFT JOIN " . $DBPrefix . "faqs_translated t ON (t.id = f.id)
			WHERE f.category = :cat AND t.lang = :languages";
	$params = array();
	$params[] = array(':cat', $cat, 'int');
	$params[] = array(':languages', $language, 'int');
	$db->query($query, $params);

	while ($row = $db->fetch())
	{
		if (!empty($row['question']) && !empty($row['answer']))
		{
			$question = $row['question'];
			$answer = $row['answer'];
		}
		else
		{
			$question = $row['q'];
			$answer = $row['a'];
		}

		$template->assign_block_vars('faqs', array(
				'Q' => $question,
				'A' => $answer,
				'ID' => $row['id']
				));
	}

	$template->set_filenames(array(
			'body' => 'viewhelp.tpl'
			));
	$template->display('body');
}
else
{
	header('location: help.php');
}
?>
