import random
from django.shortcuts import render, redirect, reverse
from .mixins import OrganisorAndLoginRequiredMixin
from leads.models import Agent
from agents.forms import AgentModelForm
from django.core.mail import send_mail
from django.views import generic





class AgentListView(generic.ListView):
    template_name = "Agent/index.html"
    context_object_name = "agents"
    def get_queryset(self):
        organisation = self.request.user.userprofile
        return Agent.objects.filter(organisation=organisation)


class AgentCreateView(OrganisorAndLoginRequiredMixin, generic.CreateView):
    template_name = "Agent/agent_create.html/"
    form_class = AgentModelForm
    def get_success_url(self):
        return reverse("agents:agent")
    
    def form_valid(self, form):
        user = form.save(commit=False)
        user.is_agent = True
        user.is_organisor = False
        user.save()
        user.organisation =self.request.user.userprofile
        user.set_password(f"{random.randint(0,100000)}")
        user.save()
        Agent.objects.create(
            user=user,
            organisation=self.request.user.userprofile,
            
            )
        send_mail(
            subject="You Anvited to be an Agent",
            message="You were added as an agent , Please come to login",
            from_email="superuser@gmail.com",
            recipient_list=[user.email]
            )
        return super(AgentCreateView, self).form_valid(form)


class AgentDetailView(OrganisorAndLoginRequiredMixin, generic.DetailView):
    template_name = "Agent/agent_detail.html/"
    context_object_name = "agent"
    def get_queryset(self):
        request_organisation = self.request.user.userprofile
        return Agent.objects.filter(organisation=request_organisation)




class AgentUpdateView(OrganisorAndLoginRequiredMixin, generic.UpdateView):
    template_name = "Agent/agent_update.html/"
    form_class = AgentModelForm
    def get_queryset(self):
        request_organisation = self.request.user.userprofile
        return Agent.objects.filter(organisation=request_organisation)
    def get_success_url(self):
        return reverse("agents:agent")


class AgentDeleteView(OrganisorAndLoginRequiredMixin, generic.DeleteView):
    template_name = "Agent/delete_agent.html/"
    def get_queryset(self):
        request_organisation = self.request.user.userprofile
        return Agent.objects.filter(organisation=request_organisation)
    def get_success_url(self):
        return reverse("agents:agent")

