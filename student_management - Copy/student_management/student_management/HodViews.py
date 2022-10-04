from django.contrib.auth import login, logout, authenticate
from django.http import HttpResponse
from django.shortcuts import render, redirect
from django.contrib.auth.decorators import  login_required




@login_required(login_url='/')
def index(req):
    return render(req, 'Hod/home.html' )




