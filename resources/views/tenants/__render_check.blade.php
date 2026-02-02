<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tenant Render Check</title>
    <style>
      body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; padding: 24px; }
      .badge { display: inline-block; padding: 6px 10px; border-radius: 8px; background: #0d6efd; color: #fff; font-weight: 700; }
      pre { background: #f6f8fa; padding: 12px; border-radius: 8px; overflow: auto; }
    </style>
  </head>
  <body>
    <!-- RENDERED: tenants.__render_check -->
    <div class="badge">TENANT BLADE RENDER CHECK: OK</div>
    <h1 style="margin-top: 16px;">If you can read this, Blade views are rendering.</h1>
    <pre>
time: {{ $time ?? '' }}
userId: {{ $userId ?? '' }}
role: {{ $role ?? '' }}
email: {{ $email ?? '' }}
route: {{ request()->path() }}
    </pre>
  </body>
</html>
