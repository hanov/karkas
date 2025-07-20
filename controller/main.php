<?php

class Controller
{
	function exec()
	{
		// задаем тайтл
		$data['title'] = 'Karkas Framework - Simple PHP Framework';

		// создаем документацию
		$data['body'] = '
		<div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; line-height: 1.6;">
			<h1 style="color: #333; border-bottom: 2px solid #333; padding-bottom: 10px;">Karkas Framework Documentation</h1>
			
			<h2 style="color: #555; margin-top: 30px;">Overview</h2>
			<p>Karkas is a lightweight PHP framework with a simple MVC-like structure. It uses SQLite database and provides basic templating functionality.</p>

			<h2 style="color: #555; margin-top: 30px;">Routing</h2>
			<p>The framework uses simple file-based routing:</p>
			<ul>
				<li><code>/</code> → <strong>controller/main.php</strong></li>
				<li><code>/ajax/method</code> → <strong>controller/ajax.php</strong> (calls specific method)</li>
				<li><code>/custom</code> → <strong>controller/custom.php</strong></li>
				<li>Non-existent routes → <strong>controller/404.php</strong></li>
			</ul>

			<h2 style="color: #555; margin-top: 30px;">Controller Structure</h2>
			<p>Each controller must have a <code>Controller</code> class with an <code>exec()</code> method:</p>
			<pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>&lt;?php
class Controller
{
    function exec()
    {
        $data[\'title\'] = \'Page Title\';
        $data[\'body\'] = \'Page content\';
        return load_tpl($data);
    }
}</code></pre>

			<h2 style="color: #555; margin-top: 30px;">Database Functions</h2>
			<p>Three main database functions are available globally:</p>
			<ul>
				<li><code>q_array($query)</code> - Returns full array of results</li>
				<li><code>query($query)</code> - Execute INSERT/UPDATE queries</li>
				<li><code>row($query)</code> - Returns single row</li>
			</ul>
			<pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>// Examples
$users = q_array("SELECT * FROM users");
query("INSERT INTO users (name, email) VALUES (\'John\', \'john@example.com\')");
$user = row("SELECT * FROM users WHERE id = 1");</code></pre>

			<h2 style="color: #555; margin-top: 30px;">Templates</h2>
			<p>Templates are stored in the <code>view/</code> directory as HTML files:</p>
			<pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>// Load template with data
$data[\'username\'] = \'John\';
$content = load_tpl(\'user-profile\', $data);

// Load global template
$page_data[\'title\'] = \'My Page\';
$page_data[\'body\'] = $content;
return load_tpl($page_data);</code></pre>

			<h2 style="color: #555; margin-top: 30px;">AJAX Methods</h2>
			<p>Add methods to <code>controller/ajax.php</code> for AJAX endpoints:</p>
			<pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>function my_ajax_method()
{
    echo json_encode([\'status\' => \'success\', \'data\' => \'Hello World\']);
}</code></pre>
			<p>Call via: <code>/ajax/my_ajax_method</code></p>

			<h2 style="color: #555; margin-top: 30px;">File Structure</h2>
			<pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>karkas/
├── controller/          # Controllers
│   ├── main.php        # Homepage
│   ├── ajax.php        # AJAX endpoints
│   └── 404.php         # Error page
├── view/               # HTML templates
│   ├── global-template.html
│   └── 404.html
├── core/
│   └── fns-min.php     # Core functions
├── model/              # Models (auto-loaded)
├── i/                  # Images/uploads
├── index.php           # Entry point
└── database.sqlite     # SQLite database</code></pre>

			<h2 style="color: #555; margin-top: 30px;">Database Configuration</h2>
			<p>Database path is configured in <code>index.php</code>:</p>
			<pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>define(\'DB_PATH\', \'database.sqlite\');</code></pre>

			<h2 style="color: #555; margin-top: 30px;">Getting Started</h2>
			
			<h3 style="color: #666; margin-top: 20px;">1. Start the PHP Development Server</h3>
			<p>Run the following command in your terminal from the framework root directory:</p>
			<pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>php -S localhost:8080</code></pre>
			<p>Then open your browser and navigate to <code>http://localhost:8080</code></p>
			
			<h3 style="color: #666; margin-top: 20px;">2. Development Steps</h3>
			<ol>
				<li>Create a new controller in <code>controller/</code> directory</li>
				<li>Add corresponding HTML template in <code>view/</code> directory</li>
				<li>Use database functions to interact with SQLite</li>
				<li>Access your page via <code>/controller-name</code></li>
			</ol>

			<div style="background: #e8f5e8; padding: 15px; border-radius: 5px; margin-top: 30px;">
				<strong>Quick Test:</strong> Try <a href="/ajax/primer">/ajax/primer</a> to see AJAX in action!
			</div>
		</div>';

		// возвращаем в глобальном шаблоне
		return load_tpl($data);
	}
}