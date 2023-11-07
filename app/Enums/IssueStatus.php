<?php
	
	namespace App\Enums;
	
	enum IssueStatus : int
	{
		case New = 0;
		case Solved = 1;
		case Denied = 2;
	}
