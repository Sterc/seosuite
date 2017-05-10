<?php
/**
 * Russian Lexicon Entries for SEO Suite
 *
 * @package seosuite
 * @subpackage lexicon
 */

$_lang['seosuite'] = 'SEO Suite';

$_lang['seosuite.menu.seosuite'] = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc'] = 'Массовое управление битыми (404) ссылками.';

$_lang['seosuite.global.search'] = 'Поиск';

$_lang['seosuite.url.urls'] = 'Битые (404) ссылки';
$_lang['seosuite.url.intro_msg'] = 'Легкое исправление ваших битых (404) ссылок, просто загрузите одноколоночный CSV-файл. SEO Suite удостоверится, что ссылки перенаправляются на правильные страницы на вашем сайте. Они будут соотнесены с уже существующими страницами вашего сайта, полагаясь на выделенную область ссылки в примере: https://example.tld/folder1/folder1/<strong>page-alias</strong>.
SEO Suite выполнит одно из этих действий:<br /><br />1. Когда есть только одно совпадение, оно будет автоматически преобразовано в перенаправление с кодом 301 в дополнении SEO Tab;<br />2. Когда есть несколько совпадений, вы можете выбрать желаемое перенаправление вручную;<br />3. Когда совпадений не найдено, вы можете ввести ссылку для перенаправления самостоятельно.';
$_lang['seosuite.url.import'] = 'Импортировать файл';
$_lang['seosuite.url.file'] = 'Файл';
$_lang['seosuite.import.start'] = 'Начинается импортирование ссылок, это может занять некоторое время в зависимости от размера вашего файла.';
$_lang['seosuite.import.instructions'] = 'Используйте .csv, .xls или .xlsx файл. Убедитесь, что ввели полные адреса ссылок, 
включая домен. Например: https://modx.org вместо modx.org.';

$_lang['seosuite.url.url'] = 'Битая (404) ссылка';
$_lang['seosuite.url.solved'] = 'Исправлено';
$_lang['seosuite.url.position'] = 'Положение';
$_lang['seosuite.url.redirect_to'] = 'Перенаправлять на';
$_lang['seosuite.url.suggestions'] = 'Соответствия для перенаправления';
$_lang['seosuite.url.find_suggestions'] = 'Найти соответствия';
$_lang['seosuite.url.found_suggestions'] = 'Мы нашли соответствие! Оно теперь связано с этой ссылкой.';
$_lang['seosuite.url.found_suggestions_multiple'] = 'Найдено более одного переадресованного совпадения.
  Пожалуйста, добавьте переадресацию вручную';
$_lang['seosuite.url.notfound_suggestions'] = 'Мы не можем найти каких-либо соответствий для этой ссылки.';
$_lang['seosuite.url.update'] = 'Обновить ссылку';
$_lang['seosuite.url.remove'] = 'Удалить ссылку';
$_lang['seosuite.url.remove_confirm'] = 'Вы действительно хотите удалить эту ссылку?';

$_lang['seosuite.error.url_alreadyexists'] = 'Эта ссылка уже существует.';
$_lang['seosuite.error.url_notfound'] = 'Элемент не найден.';
$_lang['seosuite.err.item_name_ae'] = 'Элемент не найден.';
$_lang['seosuite.error.url_notspecified'] = 'Ссылка не указана.';
$_lang['seosuite.err.item_name_ns'] = 'Значение не указано.';
$_lang['seosuite.error.url_remove'] = 'При попытке удалить ссылку произошла ошибка.';
$_lang['seosuite.error.url_save'] = 'При попытке сохранить ссылку произошла ошибка.';
$_lang['seosuite.error.emptyfile'] = 'Файл не указан.';
$_lang['seosuite.error.extension_notallowed'] = 'Такой тип файла запрещен. Только .csv, .xls или .xlsx файлы разрешены.';
$_lang['seosuite.error.ziparchive_notinstalled'] = 'Расширение PHP ZipArchive, которое необходимо, чтобы импортировать файлы xls(x), не установлено. 
Пожалуйста, установите расширение ZipArchive или используйте файл .csv.';

$_lang['seosuite.import.seoUrl.error'] = 'Соответствие не может быть сохранено как перенаправление автоматически. 
Пожалуйста, добавьте его вручную.';
$_lang['seosuite.import.seoUrl.error'] = 'Не удалось добавить соответствие к ссылке в SEO Tab, 
пожалуйста, добавьте его вручную.';
$_lang['seosuite.seotab.notfound'] = 'Дополнение SEO Tab не установлено или его версия не соответствует требуемой. 
Чтобы иметь возможность добавлять (автоматически) перенаправления для битых (404) ссылок, установите пожалуйста SEO Tab (версии 2.0 или выше).';
$_lang['seosuite.seotab.versioninvalid'] = 'Ваша версия SEO Tab устарела. 
Пожалуйста, установите SEO Tab версии 2.0 (или выше) для того, чтобы перенаправления работали правильно.';

$_lang['seosuite.widget_desc'] = 'Here you can view the 10 most recently added 404 urls.
 To view and manage all your 404 urls, please visit the <a href="[[++manager_url]]?a=home&amp;namespace=seosuite">SEO Suite manager page.</a>';
