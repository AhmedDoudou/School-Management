import datetime
from django.shortcuts import render, redirect, reverse
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.contrib.auth.views import LoginView  
from django.views.generic import CreateView, ListView, UpdateView, DeleteView, TemplateView
from inscriptions.models import Subscriptions
from core.forms import *

class SubscriptionCreateView(LoginRequiredMixin,CreateView):
    template_name = "subscription/create.html/"
    model = Subscriptions
    fields = ("__all__")
    success_url ="/"
    def get_context_data(self, **kwargs):
        context = super(SubscriptionCreateView,self).get_context_data(**kwargs)
        page_title = 'New Subscription'
        context.update({
            "page_title":page_title
         })
        return context

class SubscriptionListView(LoginRequiredMixin, ListView):
    template_name = "subscription/list.html"
    context_object_name = "list"
    model = Subscriptions
    def get_context_data(self, **kwargs):
        context = super(SubscriptionListView,self).get_context_data(**kwargs)
        page_title = 'Subscription List'
        context.update({
            "page_title":page_title
         })
        return context
    
    
class SubscriptionUpdateView(LoginRequiredMixin, UpdateView):
    model = Subscriptions
    template_name = "subscription/update.html"
    fields = '__all__'
    success_url ="/"
    def get_context_data(self, **kwargs):
        context = super(SubscriptionUpdateView,self).get_context_data(**kwargs)
        page_title = 'Update Subscription'
        context.update({
            "page_title":page_title
         })
        return context


class SubscriptionDeleteView(LoginRequiredMixin, DeleteView):
    model = Subscriptions
    template_name = "subscription/delete.html"
    context_object_name = "delete"
    success_url ="/list"
