import { Component, OnInit } from '@angular/core';
import {Router, ActivatedRoute, Params} from '@angular/router';
import {User } from '../../models/user';
import { UserService } from '../../services/user.service';
@Component({
  selector: 'login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [UserService]
})
	export class LoginComponent implements OnInit {
		public page_title: string;
		public user: User;
		public status: string;
		public token;
		public identity;
		public contador=0;
	  constructor(
	  	private _userService: UserService,
	  	private _router: Router,
	  	private _route: ActivatedRoute

	  	) { 
	this.page_title = 'identificate';
	this.user = new User(1,'', '', 'ROLE_USER', '', '','', '');

	  }
	  	

	  ngOnInit(): void {
	  	this.logout();
	  }

	  onSubmit(form){
	  	this._userService.signup(this.user).subscribe(
	  		response =>{
	  			
	  			//token
	  			if(response.status != 'error' && this.contador<5){
	  				this.status = 'success';
	  				this.token = response;
	  				//objeto usuario
	  		this._userService.signup(this.user, true).subscribe(
	  		response =>{
	  			

	  				this.identity = response;
	  				console.log(this.token);
	  				console.log(this.identity);
	  				//presistit datos	
	  				localStorage.setItem('token', this.token);
	  				localStorage.setItem('identity', JSON.stringify(this.identity));
	  			
	  				this._router.navigate(['inicio']);
	  			
	  		},
	  		error => {
	  			this.status = 'error';
	  			console.log(<any>error);
	  			

	  		}
	  		);

	  			}else{

	  				this.status = 'error';
	  				this.contador=this.contador+1;
	  			}
	  		},
	  		error => {
	  			this.status = 'error';
	  			console.log(<any>error);
	  			
	  		}
	  		);

	  }

	  logout(){
	  	this._route.params.subscribe(params =>{
	  		let logout = +params['sure'];

	  		if(logout == 1){
	  			localStorage.removeItem('identity');
	  			localStorage.removeItem('token');

	  			this.identity = null;
	  			this.token = null;

	  			//redireccion
	  			this._router.navigate(['inicio']);
	  		}
	  	});

	  }

	}
