<?php
require_once 'api.php';

$phrases  = Env::get('phrases');
$userbank = Env::get('userbank');
$page     = new Page(ucwords($phrases['edit_details']), !isset($_GET['nofullpage']));

try
{
  if(!$userbank->HasAccess(array('OWNER', 'EDIT_ADMINS')))
    throw new Exception($phrases['access_denied']);
  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    try
    {
      if($_POST['password'] != $_POST['password_confirm'])
          throw new Exception($phrases['passwords_do_not_match']);
      
      SB_API::editAdmin($_POST['id'], $_POST['name'], $_POST['auth'], $_POST['auth'] == STEAM_AUTH_TYPE ? strtoupper($_POST['identity']) : $_POST['identity'], $_POST['email'], $_POST['password']);
      
      exit(json_encode(array(
        'redirect' => Util::buildUrl(array(
          '_' => 'admin_admins.php'
        ))
      )));
    }
    catch(Exception $e)
    {
      exit(json_encode(array(
        'error' => $e->getMessage()
      )));
    }
  }
  
  $admin = SB_API::getAdmin($_GET['id']);
  
  $page->assign('admin_name',             $admin['name']);
  $page->assign('admin_type',             $admin['auth']);
  $page->assign('admin_identity',         $admin['identity']);
  $page->assign('admin_email',            $admin['email']);
  $page->assign('permission_change_pass', $userbank->HasAccess(array('OWNER')) || $_GET['id'] == $userbank->GetID());
  $page->display('page_admin_admins_editdetails');
}
catch(Exception $e)
{
  $page->assign('error', $e->getMessage());
  $page->display('page_error');
}
?>