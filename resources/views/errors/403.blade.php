<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forbidden</title>
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"; background-color: #f7fafc; color: #718096; line-height: 1.5; }
        .container { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .content { padding: 2rem; text-align: center; background-color: #fff; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        h1 { font-size: 2.25rem; font-weight: bold; color: #e53e3e; }
        h2 { margin-top: 1rem; font-size: 1.5rem; font-weight: 600; color: #2d3748; }
        p { margin-top: 0.5rem; color: #4a5568; }
        a { margin-top: 1.5rem; display: inline-block; padding: 0.5rem 1rem; font-weight: 600; color: #fff; background-color: #4299e1; border-radius: 0.25rem; text-decoration: none; }
        a:hover { background-color: #2b6cb0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h1>403</h1>
            <h2>Access Denied / Forbidden</h2>
            <p>{{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'You do not have permission to view this page.' }}</p>
            <a href="{{ url()->previous() }}">Go Back</a>
        </div>
    </div>
</body>
</html>