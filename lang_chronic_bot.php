<?php
// bot: chronicgen_bot
// name: Chronicle of day
// about/desc: Bot creates Chronicle of day
// desc: Send message to create new chronic. Use quick creation by choosing from Chronic list. Make your own Chronic list. See results in Chronicle of day.
// commands: 
// main - Show main menu
// settings - Show settings menu
// lang - Change language
// help - Show guide

$flags = ['en' => '🇬🇧', 'ru' => '🇷🇺'];
$lang = ['en' => ['hi1' => 'Hello, ', 'hi2' => ' 😋', 
	'main-back' => '⬅️ Main menu', 'main-ttl' => '🙂 Main menu', 
	'menu-hlp' => 'ℹ️ Help', 'help' => "ℹ️ Send message to bot to create new daily chronic with current date and time. 
\nTo create new daily chronic quickly - click *[Choose]* and select chronic from *[Chronic list]*. 
\n*[Chronic list]* can be edited in *[Settings]*. 
\nThe *[Result]* displays Chronicle of day. Bot immediately forgets them and you start from zero.", 
	'menu-set' => '⚙️ Settings', 'set-back' => '⬅️ Settings menu', 'set-ttl' => '⚙️ Settings menu', 
	'menu-lang' => '🔤 Language', 'lang-ask' => '🔤 Choose a language', 'lang-ok' => '✅ Language chosen', 
	'menu-list' => '🔣 Chronic list', 'list-view' => '👀 View', 'list-ttl' => "🔣 Your's chronic list", 
	'list-add' => '🆕 Add new', 'list-add-ttl' => '🆕 Enter new chronic', 'chronic-added' => '✅ Chronic added', 
	'list-del' => '❌ Delete', 'list-del-ttl' => '❌ Choose chronic to delete', 'chronic-deleted' => '❌ Chronic deleted', 
	'menu-res' => '📅 Result', 'res-ttl' => '📅 Chronicle of day', 'res-empty' => ' is empty', 
	'menu-chus' => '▶️ Choose', 'chus-ttl' => '⏩ Choose chronic', 'chronic-saved' => '✅ Chronic saved', 
	'comment-saved' => '✅ Comment saved', 'chronic-notfound' => '👻 Chronic not found', 
	'default' => '👻 IDK, what to say...'], 

	'ru' => ['hi1' => 'Привет, ', 'hi2' => ' 😋', 
	'main-back' => '⬅️ Главное меню', 'main-ttl' => '🙂 Главное меню', 
	'menu-hlp' => 'ℹ️ Помощь', 'help' => "ℹ️ Отправьте сообщение боту для создания новой хроники дня с текущей датой-временем. 
\nЧтобы создать новую хронику дня быстро - нажмите *[Выбрать]* и выберите хронику из *[Списка хроник]*. 
\n*[Список хроник]* можно отредактировать в *[Настройках]*. 
\nКнопка *[Результат]* выводит Хронику дня. Бот сразу их забывает и вы начинаете с нуля.", 
	'menu-set' => '⚙️ Настройки', 'set-back' => '⬅️ Меню настроек', 'set-ttl' => '⚙️ Меню настроек', 
	'menu-lang' => '🔤 Язык', 'lang-ask' => '🔤 Выбери язык', 'lang-ok' => '✅ Язык выбран', 
	'menu-list' => '🔣 Список хроник', 'list-view' => '👀 Просмотр', 'list-ttl' => '🔣 Ваш список хроник', 
	'list-add' => '🆕 Добавить', 'list-add-ttl' => '🆕 Введите новую хронику', 'chronic-added' => '✅ Хроника добавлена', 
	'list-del' => '❌ Удалить', 'list-del-ttl' => '❌ Выберите хронику для удаления', 'chronic-deleted' => '❌ Хроника удалена', 
	'menu-res' => '📅 Результат', 'res-ttl' => '📅 Хроника дня', 'res-empty' => ' пуста', 
	'menu-chus' => '▶️ Выбрать', 'chus-ttl' => '⏩ Выберите хронику', 'chronic-saved' => '✅ Хроника сохранена', 
	'comment-saved' => '✅ Комментарий сохранен', 'chronic-notfound' => '👻 Нет такой хроники', 
	'default' => '👻 Я не знаю, что сказать...']];
?>