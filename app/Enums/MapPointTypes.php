<?php
	
	namespace App\Enums;
	
	enum MapPointTypes : int
	{
		case City = 1;
		case County = 2;
		case ManagedBy = 3;
		case Address = 4;
		case OffersMoney = 5;
		case OffersTransport = 6;
		case OpeningHours = 7;
		
		case Email = 8;
		case Website = 9;
		case Notes = 10;
		case NoAddress = 11;
		case PhoneNo = 12;
		case LocationPrivateNotes = 13;
		case OffersVoucher = 14;
		case ServiciiGratis = 15;
	}
