from django.shortcuts import render, redirect, reverse
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.views.generic import CreateView, ListView, UpdateView, DeleteView
from inscriptions.models import Contacts



class ContactCreateView(LoginRequiredMixin, CreateView):
    template_name = "contact/create.html/"
    model = Contacts
    fields = ("__all__")
    success_url ="/"
    

class ContactListView(LoginRequiredMixin, ListView):
    template_name = "contact/list.html"
    context_object_name = "list"
    model = Contacts
    success_url ="/"

class ContactUpdateView(LoginRequiredMixin,UpdateView):
    model = Contacts
    template_name = "contact/update.html"
    fields = '__all__'
    success_url ="/"


class ContactDeleteView(LoginRequiredMixin, DeleteView):
    model = Contacts
    template_name = "contact/delete.html"
    context_object_name = "delete"
    success_url ="dashboard"
