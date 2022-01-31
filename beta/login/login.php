<?php
	// Set the response header.
	header("Content-Type: application/json; charset: UTF-8;");
    
    // Start the session.
	session_start();
    
    // Verify if already exist the login session.
    if(isset($_SESSION["loginInformaion"]) == true){
		if($_SESSION["loginInformaion"] -> alreadyLogged === true){
			responseContent(200, "Already Logged.", newAction("Redirect", "Redirect the user in profile page.", $_SESSION["loginInformaion"] -> userProfilePage));
		}
	}
	
    // Creating the filePath variable.
	$filePath = "users.json";

	// Reading of the body (sent as a file), conversion into a string and subsequently into an object.
	$bodyRequest = json_decode(file_get_contents("php://input"), true);

	// Reading of the users file, conversion into an array.
	$users = json_decode(file_get_contents($filePath), true);
    
	switch ($bodyRequest["operation"]) {
		case "verify_SignIn":
			if (array_key_exists($bodyRequest["data"]["username"], $users) == false) {
				responseError(401, "Unauthorized: wrong username or password.", "Verify that you have typed your credentials correctly and try again.", null);
			} else {
				if (hash("sha256", $bodyRequest["data"]["password"]) != $users[$bodyRequest["data"]["username"]]["password"]) {
					responseError(401, "Unauthorized: wrong username or password.", "Verify that you have typed your credentials correctly and try again.", null);
				} else {
					// Login successed
                    $userType = strtolower(getUserType());
					$newURL = "../profile/$userType.html";
					
                    //Session for validating login
                    $loginInformaion -> alreadyLogged = true;
                    $loginInformaion -> userProfilePage = $newURL;
                    $_SESSION["loginInformaion"] = $loginInformaion;
                    
                    //Session for user information
                    $userInformation -> userUsername = $bodyRequest["data"]["username"];
                    $userInformation -> lastname = explode(".", $bodyRequest["data"]["username"])[0];
                    $userInformation -> firstname = explode(".", $bodyRequest["data"]["username"])[1];
                    $userInformation -> userType = $userType;
                    $userInformation -> profilePage = $newURL;
                    $_SESSION["userInformation"] = $userInformation;
                    
                    responseContent(200, "Login Successed.", newAction("Redirect", "Redirect the user in profile page.", $newURL));
				}
			}
		break;
		case "verify_SignUp":
			if (array_key_exists($bodyRequest["data"]["username"], $users) == true) {
				responseError(409, "Conflict: the username is already registered.", "Use a different username or sign in.", null);
			} else {
				$users[$bodyRequest["data"]["username"]] = array("userType" => strtolower(getUserType()), "password" => hash("sha256", $bodyRequest["data"]["password"]));
				if (array_key_exists($bodyRequest["data"]["username"], $users) == false) {
					responseError(500, "Internal Server Error: the server encountered an error while adding user data. Registration was canceled.", "Try running the request again.", null);
				} else {
					$status = file_put_contents($filePath, json_encode($users));
					if ($status <= 0) {
						responseError(500, "Internal Server Error: the server encountered an error while updating data in the database. Registration was canceled.", "Try running the request again.", null);
					} else {
						responseContent(200, "Registration was successful.", newAction("Reload", "Reaload the page on login form.", null));
					}
				}
			}
		break;
		default:
        	
            responseError (405, "Unknown operation.", "Verify that you have typed the operation value correctly and try again.", null);
	}

	// Procedure for returning an error and enclosed solution.
	function responseError ($status, $error, $solution, $action) {
		$response -> status = $status;
		$response -> error = $error;
		$response -> solution = $solution;
		$response -> action = $action;
		echo json_encode($response);
		http_response_code($response -> status);
        return $response -> status;
	}

	// Procedure for returning a valid response.
	function responseContent ($status, $content, $action) {
		$response -> status = $status;
		$response -> content = $content;
		$response -> action = $action;
		echo json_encode($response);
		http_response_code($response -> status);
        return $response -> status;
	}

	// Function to generate action for response.
	function newAction ($title, $description, $information) {
		$action -> title = $title;
		$action -> description = $description;
		$action -> information = $information;
		return $action;
	}

	// Function to get the type of user.
	function getUserType () {
    	//var_dump($bodyRequest["data"]["username"]);
        //echo "\r\n";
        //var_dump(explode(".", $bodyRequest["data"]["username"]));
        //echo "\r\n";
        //var_dump(explode(".", $bodyRequest["data"]["username"])[2]);
        //echo "\r\n";
        //var_dump(strpos(explode(".", $bodyRequest["data"]["username"])[2], "S"));
        //echo "\r\n";
        //var_dump(strpos(explode(".", $bodyRequest["data"]["username"])[2], "T"));
        //echo "\r\n";
        
		if (strpos(explode(".", $bodyRequest["data"]["username"])[2], "S") !== false) {
			return "Student";
		} else if (strpos(explode(".", $bodyRequest["data"]["username"])[2], "T") !== false) {
			return "Teacher";
		}
        responseContent(500, "Unrecognized account type.", "Try running the request again or verify that the username entered is correct.", null);
        return "index";
	}
	
    /* JSON request example:
    	{
        	"Davide.Vanoncini.18S008": {
            	"type": "Student",
                "password": "5c86651b500d1b24b86a4054262842058953a8457880b886dbe8bbaa9edfbe5f"
            },
            "Morandi.Andrea.16T063": {
            	"type": "Teacher",
                "password": "6dfbe5f5c8b86a4054058954be8b66513a8457880b882628b500d1b24baa9ed2"
            }
        }
    */
	/* JSON response example:
		//Response Error:
        {
			"status": 500,
            "error": "Internal Server Error: the server encountered an error while adding user data. Registration was canceled.",
            "solution": "Try running the request again.",
            "action": null
		}
        //Response Content:
        {
        	"status": 200,
            "content": "Login Successed.",
            "action": {
            	"title": "Redirect",
                "description": "Redirect the user in profile page.",
                "information": "../profile/student.html"
            }
        }
	*/
?>