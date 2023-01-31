<template>
  <!DOCTYPE html>
  <html>
    <head>
      <meta charset="UTF-8" />
      <title>User Registration</title>
      <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      />
    </head>
    <body>
      <main class="container">
        <h1>User Registration</h1>

        <form method="post">
          <label for="name">
            Name
            <input name="name" id="name" />
          </label>

          <label for="username">
            Username
            <input name="username" id="username" />
          </label>

          <label for="password">
            Password
            <input type="password" name="password" id="password" />
          </label>

          <button>Register</button>
        </form>
      </main>
    </body>
  </html>
</template>
