<?php
require_once "utils/helper.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./dist/css/style.css">
  <script src="./dist/js/htmx.min.js"></script>
</head>

<body>
  <div class="max-w-[400px] mx-auto">
    <div class="py-10 px-[10px]">
      <h1 class="text-center text-[20px]">HTMX PHP Todo App</h1>
      <div class="mt-[20px]">
        <form id="todoForm" hx-post="./actions/?type=create_todo" hx-target="#todoItems" hx-trigger="submit">
          <div class="flex items-end">
            <label class="w-full">
              <input type="text" name="todo_desc" class="w-full px-[10px] py-[6px] border-[1px] rounded-[6px] text-[16px]" placeholder="Enter todo description" required>
            </label>
            <button class="flex-none rounded-[6px] bg-gray-200 hover:shadow-md transition-all px-[12px] py-[7px] ml-[10px]">Add Todo</button>
          </div>
        </form>
      </div>
      <ul id="todoItems" class="mt-[20px]">
        <?php
        // fetch and display saved todos
        $todos = read_database();
        $todos_items = render_todo_items($todos);
        print_r($todos_items);
        ?>
      </ul>
    </div>
  </div>
  <script>
    (function() {
      // event to detect when ajax request is completed
      document.addEventListener('htmx:afterRequest', function(ev) {
        // failed request
        if (ev.detail.failed === true) {
          // alert error if request failed
          alert(ev.detail.xhr.response || "An error occurred")
        }

        // successful request
        if (ev.detail.successful === true) {
          // clear form for a successful request
          if (ev.detail.elt === document.querySelector("#todoForm")) todoForm.reset()
        }
      })
    })()
  </script>
</body>

</html>