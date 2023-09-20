<?php
require_once '../utils/helper.php';

// handling todo creation
if ($_GET['type'] === 'create_todo') {
  try {
    // destructure post request array
    list(
      'todo_desc' => $todo_desc
    ) = $_POST;

    $todos = save_to_database($todo_desc);

    $todos_items = render_todo_items($todos);

    print_r($todos_items);
    exit;
  } catch (Exception $err) {
    error_handler($err->getMessage(), ($err->getCode() != '0' ? $err->getCode() : 500));
  }
}

// handling mark todo done = true | false
if ($_GET['type'] === 'mark_todo') {
  try {
    // destructure get request array
    list(
      'todo_id' => $todo_id
    ) = $_GET;

    $todos = update_database($todo_id);

    $todos_items = render_todo_items($todos);

    print_r($todos_items);
    exit;
  } catch (Exception $err) {
    error_handler($err->getMessage(), ($err->getCode() != '0' ? $err->getCode() : 500));
  }
}

// handling todo deletion
if ($_GET['type'] === 'delete_todo') {
  try {
    // destructure get request array
    list(
      'todo_id' => $todo_id
    ) = $_GET;

    $todos = delete_database($todo_id);

    $todos_items = render_todo_items($todos);

    print_r($todos_items);
    exit;
  } catch (Exception $err) {
    error_handler($err->getMessage(), ($err->getCode() != '0' ? $err->getCode() : 500));
  }
}

// unknown action
not_found('action type does not exist');
