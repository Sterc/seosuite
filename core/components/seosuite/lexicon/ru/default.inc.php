<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$_lang['seosuite']                                              = 'SEO Suite';
$_lang['seosuite.menu.seosuite']                                = 'SEO Suite';
$_lang['seosuite.menu.seosuite_desc']                           = 'Управление URL-адресами 404 и перенаправлениями.';

$_lang['area_seosuite']                                         = 'SEO Suite';
$_lang['area_seosuite_tab_meta']                                = 'Мета-вкладка SEO Suite';
$_lang['area_seosuite_tab_seo']                                 = 'Вкладка поисковой системы SEO Suite';
$_lang['area_seosuite_tab_social']                              = 'Социальная вкладка SEO Suite';
$_lang['area_seosuite_sitemap']                                 = 'Карта сайта SEO Suite';

$_lang['seosuite.urls']                                         = '404 URL';
$_lang['seosuite.urls_desc']                                    = 'Исправить URL-адреса 404 очень просто. Просто загрузите CSV-файл с одной колонкой.
 SEO Suite обеспечит перенаправление URL-адресов на нужную страницу вашего сайта.
  Они будут сопоставлены с существующими страницами вашего веб-сайта на основе выделенной жирным шрифтом части URL-адреса примера: https://example.tld/folder1/folder1/ <strong> page-alias </strong>.
   SEO Suite выполнит одно из следующих действий: <br/> <br/>
   1. Когда есть ровно одно совпадение, оно будет автоматически преобразовано в 301 редирект на вкладке SEO, или, если вкладка SEO не установлена, SEO Suite обработает перенаправление; <br/>
   2. Если совпадений несколько, вы можете выбрать желаемый редирект вручную; <br/>
   3. Если совпадений нет, вы можете ввести URL-адрес для перенаправления на себя. ';
$_lang['seosuite.urls_import']                                  = 'Импортировать URL-адреса 404';
$_lang['seosuite.urls_remove']                                  = 'Удалить URL-адреса 404';
$_lang['seosuite.urls_remove_confirm']                          = 'Вы действительно хотите удалить выбранные URL-адреса 404?';
$_lang['seosuite.url_update']                                   = 'Обновить URL-адрес 404';
$_lang['seosuite.url_suggestions']                              = '404 предложения URL';
$_lang['seosuite.url.found_suggestions']                        = 'Предложение найдено! Предложение теперь связано с этим URL. ';
$_lang['seosuite.url.found_suggestions_multiple']               = 'Найдено более 1 предложения. Пожалуйста, добавьте перенаправление вручную. ';
$_lang['seosuite.url.notfound_suggestions']                     = 'Для этого URL не найдено предложений.';
$_lang['seosuite.url.found_suggestions.redirect_exists']        = 'Для этой страницы 404 уже существует перенаправление. URL-адрес 404 удален. ';
$_lang['seosuite.url_remove']                                   = 'Удалить URL';
$_lang['seosuite.url_remove_confirm']                           = 'Вы уверены, что хотите удалить этот URL?';
$_lang['seosuite.url.redirect_to_selected']                     = 'Выбранное перенаправление';
$_lang['seosuite.suggestions_found']                            = '[[+suggestions]] найдены варианты страниц.';
$_lang['seosuite.suggestion_boost']                             = 'баллы';
$_lang['seosuite.exclude_words']                                = 'Исключить слова';

$_lang['seosuite.label_exclude_words']                          = 'Исключить слова';
$_lang['seosuite.label_exclude_words_desc']                     = 'Разделенный запятыми список слов, исключаемых из предложений URL 404.';

$_lang['seosuite.label_url_url']                                = 'URL';
$_lang['seosuite.label_url_url_desc']                           = '';
$_lang['seosuite.label_url_visits']                             = 'Попадания';
$_lang['seosuite.label_url_visits_desc']                        = '';
$_lang['seosuite.label_url_last_visit']                         = 'Последнее попадание';
$_lang['seosuite.label_url_last_visit_desc']                    = '';
$_lang['seosuite.label_url_suggestion']                         = 'Предложение';
$_lang['seosuite.label_url_suggestion_desc']                    = 'Предложение перенаправить URL-адрес на.';
$_lang['seosuite.label_url_suggestions']                        = 'Предложения';
$_lang['seosuite.label_url_suggestions_desc']                   = '';
$_lang['seosuite.label_url_createdon']                          = 'Создано';
$_lang['seosuite.label_url_createdon_desc']                     = '';
$_lang['seosuite.label_url_match_context']                      = 'Соответствует домену [[+domain]]';
$_lang['seosuite.label_url_match_context_desc']                 = 'Если отмечено, соответствие будет происходить в домене [[+domain]]. Это особенно полезно при наличии нескольких веб-сайтов, где несколько контекстов содержат много одинаковых страниц. ';
$_lang['seosuite.label_url_match_create_redirect']              = 'Автоматически создавать перенаправление URL';
$_lang['seosuite.label_url_match_create_redirect_desc']         = 'Автоматически создаст перенаправление URL-адреса, если будет найдено предложение.';

$_lang['seosuite.label_import_file']                            = 'Файл';
$_lang['seosuite.label_import_file_desc']                       = 'Выберите допустимый файл для импорта URL-адресов 404.';
$_lang['seosuite.import.instructions']                          = 'Используйте файл .csv, .xls или .xlsx. Убедитесь, что вы ввели полные URL-адреса, включая домен. Пример: https://www.seosuite.com вместо seosuite.com. Вы также можете просмотреть этот <a href="[[+pathpting ]"> пример файла импорта (XLS) </a>. ';

$_lang['seosuite.redirect']                                     = 'Перенаправление URL';
$_lang['seosuite.redirects']                                    = 'Перенаправления URL-адресов';
$_lang['seosuite.redirects_desc']                               = 'Здесь можно управлять всеми перенаправлениями URL-адресов. Перенаправление URL-адреса используется для перенаправления несуществующей страницы на новую страницу. Перенаправление URL-адреса может быть выполнено для определенного контекста или для всех контекстов сразу и будет отображаться ниже как «* /». Перенаправление URL-адресов с определенным контекстом имеет приоритет перед перенаправлением URL-адресов для всех контекстов. ';
$_lang['seosuite.redirect_create']                              = 'Новое перенаправление URL';
$_lang['seosuite.redirect_update']                              = 'Обновить перенаправление URL';
$_lang['seosuite.redirect_remove']                              = 'Удалить перенаправление URL';
$_lang['seosuite.redirect_remove_confirm']                      = 'Вы уверены, что хотите удалить это перенаправление URL?';
$_lang['seosuite.redirects_remove']                             = 'Удалить перенаправление (я) URL';
$_lang['seosuite.redirects_remove_confirm']                     = 'Вы действительно хотите удалить выбранные перенаправления URL-адресов?';
$_lang['seosuite.use_redirect_across_domains']                  = 'Использовать перенаправление для всех доменов';

$_lang['seosuite.label_redirect_old_url']                       = 'Старый URL';
$_lang['seosuite.label_redirect_old_url_desc']                  = 'Старый URL-адрес перенаправления URL-адреса без имени домена. Например: «/example». ';
$_lang['seosuite.label_redirect_new_url']                       = 'Новый URL';
$_lang['seosuite.label_redirect_new_url_desc']                  = 'Новый URL-адрес перенаправления URL-адреса. Он может быть с доменом или без него, но также может содержать идентификатор ресурса. ';
$_lang['seosuite.label_redirect_active']                        = 'Активный';
$_lang['seosuite.label_redirect_active_desc']                   = '';
$_lang['seosuite.label_redirect_match_context']                 = 'Контекст';
$_lang['seosuite.label_redirect_match_context_desc']            = 'Сопоставьте перенаправление с определенным доменом или используйте перенаправление как подстановочный знак для всех доменов.';
$_lang['seosuite.label_redirect_type']                          = 'Тип перенаправления';
$_lang['seosuite.label_redirect_type_desc']                     = 'Тип перенаправления URL-адреса перенаправления.';

$_lang['seosuite.error.emptyfile']                              = 'Файл не указан.';
$_lang['seosuite.error.extension_notallowed']                   = 'Тип файла не разрешен. Разрешены только файлы .csv, .xls или .xlsx. ';
$_lang['seosuite.error.ziparchive_notinstalled']                = 'Не установлено расширение PHP ZipArchive, которое необходимо для импорта файлов xls(x). Установите расширение ZipArchive или используйте файл .csv. ';

$_lang['seosuite.friendly_urls_disabled']                       = 'Дружественные для поисковых систем URL-адреса в настоящее время не включены в этой установке MODx, для правильной работы SEO Suite должны быть включены дружественные URL-адреса.';
$_lang['seosuite.find_suggestions']                             = 'Найти предложения';
$_lang['seosuite.redirect_error_exists']                        = 'Для этого URL-адреса уже существует перенаправление.';
$_lang['seosuite.resource_no_redirects']                        = 'Для этой страницы нет перенаправления URL.';
$_lang['seosuite.time_seconds']                                 = 'Менее 1 минуты назад';
$_lang['seosuite.time_minute']                                  = '1 минуту назад';
$_lang['seosuite.time_minutes']                                 = '[[+minutes]] минут назад';
$_lang['seosuite.time_hour']                                    = '1 час назад';
$_lang['seosuite.time_hours']                                   = '[[+hours]] часов назад';
$_lang['seosuite.time_day']                                     = '1 день назад';
$_lang['seosuite.time_days']                                    = '[[+ days]] дней назад';
$_lang['seosuite.time_week']                                    = '1 неделю назад';
$_lang['seosuite.time_weeks']                                   = '[[+weeks]] недель назад';
$_lang['seosuite.time_month']                                   = '1 месяц назад';
$_lang['seosuite.time_months']                                  = '[[+months]] месяцев назад';
$_lang['seosuite.time_to_long']                                 = 'Более полугода назад';

$_lang['seosuite.widget_desc']                                  = 'Здесь вы можете просмотреть 10 последних добавленных URL 404. Для просмотра и управления всеми вашими 404 URL-адресами посетите страницу <a href="[[++manager_url visible ]?a=home&amp;namespace=seosuite">SEO Suite</a> ';
