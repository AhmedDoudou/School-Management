from django.shortcuts import render, redirect, reverse
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.views.generic import CreateView, ListView, UpdateView, DeleteView
from inscriptions.models import Programs


class ProgramCreateView(LoginRequiredMixin, CreateView):
    template_name = "program/create.html/"
    model = Programs
    fields = ("__all__")
    success_url ="/"
    

class ProgramListView(LoginRequiredMixin, ListView):
    template_name = "program/list.html"
    context_object_name = "list"
    model = Programs
    success_url ="/"

class ProgramUpdateView(LoginRequiredMixin,UpdateView):
    model = Programs
    template_name = "program/update.html"
    fields = '__all__'
    success_url ="program/"


class ProgramDeleteView(LoginRequiredMixin, DeleteView):
    model = Programs
    template_name = "program/delete.html"
    context_object_name = "delete"
    success_url ="program/"
