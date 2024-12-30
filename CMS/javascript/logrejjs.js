
		function myFunction() 
		{
			document.getElementById("logowanie").style.display = "none";
			document.getElementById("rejestracja").style.display = "block";
			document.getElementById("loguj").style.display = "block";
			document.getElementById("rejestruj").style.display = "none";
		}
		
		function myFunction2() 
		{
		document.getElementById("logowanie").style.display = "block";
		document.getElementById("rejestracja").style.display = "none";
		document.getElementById('loguj').style.display = "none";
		document.getElementById("rejestruj").style.display = "block";
		}
        function  loginlogf()
		{
			var regex = new RegExp("^[0-9a-zA-Z]{8,16}$"); 
			if(regex.test(document.getElementById("login").value))
			{
				document.getElementById("login").style.border ="1px solid green";
				document.getElementById("login").style.boxShadow ="0 0 1px green";
				document.getElementById("loginmistake").style.display="none";
				
				
			}		
			else 
			{document.getElementById("login").style.border ="1px solid red";
				document.getElementById("login").style.boxShadow ="0 0 3px #8B0000";
				document.getElementById("loginmistake").innerHTML="(Tylko litery i cyfry,od 8 do 16 znaków)";
				$("#loginmistake").fadeIn();
			}
			
		}
		function haslologf()
		{
			var regex = new RegExp("^.{8,20}$");
			var regex4 = new RegExp("[a-z]");
			var regex2 = new RegExp("[A-Z]");
			var regex3 = new RegExp("[0-9]");
			if( regex.test(document.getElementById("haslo").value) && regex4.test(document.getElementById("haslo").value) && regex2.test(document.getElementById("haslo").value) && regex3.test(document.getElementById("haslo").value))
			{
					document.getElementById("haslo").style.border ="1px solid green";
					document.getElementById("haslo").style.boxShadow ="0 0 1px green";
					document.getElementById("passwdmistake").style.display="none";
			}		
			else {
					document.getElementById("haslo").style.border ="1px solid red";
					document.getElementById("haslo").style.boxShadow ="0 0 3px #8B0000";
					document.getElementById("passwdmistake").innerHTML="(od 8 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra)";
					$("#passwdmistake").fadeIn();
				}
		}
		function same()
		{
			if(document.getElementById("haslo2").value == document.getElementById("haslo").value)
			{
				document.getElementById("haslo2").style.border ="1px solid green";
				document.getElementById("haslo2").style.boxShadow ="0 0 1px green";
				document.getElementById("passwdrepeat").style.display="none";
			}
			else
			{
				document.getElementById("haslo2").style.border ="1px solid red";
				document.getElementById("haslo2").style.boxShadow ="0 0 3px #8B0000";
				document.getElementById("passwdrepeat").innerHTML="(hasła nie są identyczne)";
					$("#passwdrepeat").fadeIn();
			}
		}
		function emailf()
		{
			var validRegex = new RegExp(/^[a-zA-Z0-9.!#$%&'*+=?^_`{|}~-]+@[a-zA-Z0-9]+\.[?:\.a-zA-Z0-9]+$/);
				if(validRegex.test(document.getElementById("email").value))
				{
				document.getElementById("email").style.border ="1px solid green";
				document.getElementById("emailcheck").style.display="none";
				document.getElementById("email").style.boxShadow ="0 0 1px green";
			}
			else
			{
				document.getElementById("email").style.border ="1px solid red";
				document.getElementById("email").style.boxShadow ="0 0 3px #8B0000";
				document.getElementById("emailcheck").innerHTML="(podany email jest błędny)";
					$("#emailcheck").fadeIn();
			}
		}
		function isnull(field,text)
		{
			var regexlogin = new RegExp(/^[a-zA-Z0-9.!#$%&'*+=?^\/_`{|}~-]+$/);
			if(regexlogin.test(field.value))
			{
				field.style.border ="1px solid green";
				text.style.display="none";
				field.style.boxShadow="0 0 1px green";
			}
			else{
				field.style.border ="1px solid red";
				field.style.boxShadow="0 0 3px #8B0000";
				text.innerHTML="(pole "+field.id.substring(0,5)+ " nie może być puste)";
					//$("#islogin").fadeIn();
					$(text).fadeIn();
			}
		}
		
			function odstep(){
				if(document.getElementById("islogin").style.display=="none")
					{
					document.getElementsByClassName("odstep").style.marginBottom="13px";
					}
				else
					{
						document.getElementsByClassName("odstep").style.marginBottom="0";
					}
								}
	
