<?php
date_default_timezone_set("Africa/Lagos");

function read_database()
{
  try {
    $todos = file_get_contents(__DIR__ . "/../" . "database.json");

    $decodedTodos = json_decode($todos, true);
    if ($decodedTodos === null) {
      throw new Exception('error decoding todos');
    }

    return $decodedTodos;
  } catch (Exception $err) {
    throw new Exception($err->getMessage(), ($err->getCode() != '0' ? $err->getCode() : 500));
  }
}

function save_to_database(string $todo_desc)
{
  try {
    $todos = read_database();

    $todos[] = [
      'id' => ($todos[count($todos) - 1]['id'] ?? 0) + 1,
      'desc' => $todo_desc,
      'done' => false,
      'timestamp' => time()
    ];

    $encodedTodos = json_encode($todos, JSON_PRETTY_PRINT);
    if ($encodedTodos === null) {
      throw new Exception('error encoding todos');
    }

    if (!file_put_contents(__DIR__ . "/../" . "database.json", $encodedTodos)) {
      throw new Exception('error writing to database');
    }

    return $todos;
  } catch (Exception $err) {
    throw new Exception($err->getMessage(), ($err->getCode() != '0' ? $err->getCode() : 500));
  }
}

function update_database(int $todo_id)
{
  try {
    $todos = read_database();

    foreach ($todos as &$todo) {
      if ($todo['id'] == $todo_id) {
        $todo['done'] = !$todo['done'];
        break;
      }
    }

    $encodedTodos = json_encode($todos, JSON_PRETTY_PRINT);
    if ($encodedTodos === null) {
      throw new Exception('error encoding todos');
    }

    if (!file_put_contents(__DIR__ . "/../" . "database.json", $encodedTodos)) {
      throw new Exception('error writing to database');
    }

    return $todos;
  } catch (Exception $err) {
    throw new Exception($err->getMessage(), ($err->getCode() != '0' ? $err->getCode() : 500));
  }
}

function delete_from_database(int $todo_id)
{
  try {
    $todos = read_database();

    $index_to_delete = null;
    foreach ($todos as $index => $todo) {
      if ($todo['id'] == $todo_id) {
        $index_to_delete = $index;
        break;
      }
    }

    if ($index_to_delete === null) {
      throw new Exception("no todo with such id", 400);
    }

    // remove todo from todos
    array_splice($todos, $index_to_delete, 1);

    $encodedTodos = json_encode($todos, JSON_PRETTY_PRINT);
    if ($encodedTodos === null) {
      throw new Exception('error encoding todos');
    }

    if (!file_put_contents(__DIR__ . "/../" . "database.json", $encodedTodos)) {
      throw new Exception('error writing to database');
    }

    return $todos;
  } catch (Exception $err) {
    throw new Exception($err->getMessage(), ($err->getCode() != '0' ? $err->getCode() : 500));
  }
}

function render_todo_items(array $todos)
{
  $todos = array_reverse($todos);

  $todo_items = '';

  if (count($todos) > 0) {
    foreach ($todos as $todo) {

      $checked = $todo['done'] == true ? "checked" : '';
      $line_through = $todo['done'] == true ? "line-through" : '';
      $date = date('Y-m-d h:i:s a', $todo['timestamp']);

      $todo_items .= <<<HTML
        <li class="pr-[10px] border-[1px] rounded-[4px] mb-[5px] flex items-center justify-between">
          <label class="px-[10px] py-[10px] cursor-pointer w-full">
            <input type="checkbox" id="todoItem{$todo['id']}" class="hidden" hx-get="./actions/?type=mark_todo&todo_id={$todo['id']}" hx-target="#todoItems" hx-trigger="change" {$checked}>
            <p class="text-[12px] leading-4 text-gray-300 font-[300]">{$date}</p>
            <p class="leading-5 mt-[5px] {$line_through}">{$todo['desc']}</p>
          </label>
          <div class="font-semibold">
            <button title="Delete" class="px-[8px] py-[2px] border-[1px] rounded-[6px] hover:shadow-md text-red-600 bg-white"  hx-get="./actions/?type=delete_todo&todo_id={$todo['id']}" hx-target="#todoItems" hx-trigger="click">D</button>
          </div>
        </li>
      HTML;
    }
  } else {
    $todo_items .= <<<HTML
      <li class="text-center text-gray-400 italic">No todo item yet</li>
    HTML;
  }

  return $todo_items;
}

function error_handler(string $message = null, int $http_code = 500)
{
  header('HTTP/1.1 ' . $http_code);
  print_r('Error: ' . ($message ?? 'internal server error'));
  exit;
}

function not_found()
{
  error_handler('url not found', 404);
}
