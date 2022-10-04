import datetime
from django.shortcuts import render, redirect, reverse
from django.contrib.auth.mixins import LoginRequiredMixin
from django.core.mail import send_mail
from .models import Lead , Agent, Category, FollowUp
from agents.mixins import OrganisorAndLoginRequiredMixin
from .form import LeadModelForm, CustomUserCreationForm, AssignAgentForm, FollowUpModelForm
from django.views import generic

class SignupView(generic.CreateView):
    template_name = "registration/signup.html/"
    form_class = CustomUserCreationForm
    def get_success_url(self):
        return reverse("login")
class DashbordView(generic.TemplateView):
    template_name = "dashbord.html"

    def get_context_data(self, **kwargs):
        context = super(DashbordView, self).get_context_data(**kwargs)
        user = self.request.user
        # counting Total of Leads
        total_leads = Lead.objects.filter(organisation=user.userprofile).count()
        # Leads in 30 days ago
        thirty_days = datetime.date.today() - datetime.timedelta(days=30)
        last_thirty_days = Lead.objects.filter(
            organisation=user.userprofile,
            date_added__gte=thirty_days
        ).count()
        context.update({
            "total_leads": total_leads,
            "last_thirty_days": last_thirty_days
        })

        return context

class LeadListView(LoginRequiredMixin, generic.ListView):
    template_name = "Lead/index.html"
    context_object_name = "leads"
    def get_queryset(self):
         user = self.request.user
         if user.is_organisor:
            queryset = Lead.objects.filter(organisation=user.userprofile, agent__isnull=False)
         else:
            queryset = Lead.objects.filter(organisation=user.agent.organisation, agent__isnull=False)
            queryset = queryset.filter(agent__user=user)
         return queryset
    
    def get_context_data(self, **kwargs):
        context = super(LeadListView, self).get_context_data(**kwargs)
        user = self.request.user
        if user.is_organisor:
            queryset = Lead.objects.filter(organisation=user.userprofile, agent__isnull=True)
            context.update({"unassigned_leads":queryset})
        return context



class LeadDetailView(OrganisorAndLoginRequiredMixin, generic.DetailView):
    template_name = "Lead/lead_detail.html/"
    context_object_name = "lead"
    def get_queryset(self):
         user = self.request.user
         if user.is_organisor:
            queryset = Lead.objects.filter(organisation=user.userprofile)
         else:
            queryset = Lead.objects.filter(organisation=user.agent.organisation)
            queryset = queryset.filter(agent__user=user)
         return queryset

class LeadCreateView(OrganisorAndLoginRequiredMixin, generic.CreateView):
    template_name = "Lead/lead_create.html/"
    form_class = LeadModelForm
    def get_success_url(self):
        return reverse("leads:home")
    
    def form_valid (self, form):
        lead = form.save(commit=False) 
        lead.organisation =self.request.user.userprofile
        lead.save()
        send_mail(
            subject=" A Lead has been created",
            message=" Go to site to see new lead ",
            from_email=" test@test.com ",
            recipient_list=["test2@test.com"]
        )
        return super(LeadCreateView, self).form_valid(form)

class LeadUpdateView(OrganisorAndLoginRequiredMixin, generic.UpdateView):
    template_name = "Lead/update_lead.html/"
    form_class = LeadModelForm
    def get_success_url(self):
        return reverse("leads:home")
    def get_queryset(self):
        user = self.request.user            
        return Lead.objects.filter(organisation=user.agent.organisation)
        

class LeadDeleteView(OrganisorAndLoginRequiredMixin, generic.DeleteView):
    template_name = "Lead/delete_lead.html/"
    def get_success_url(self):
        return reverse("leads:home")
    def get_queryset(self):
        user = self.request.user            
        return Lead.objects.filter(organisation=user.agent.organisation)



class AssignAgentView(OrganisorAndLoginRequiredMixin, generic.FormView):
    template_name = "Lead/assign_agent.html"
    form_class = AssignAgentForm

    def get_form_kwargs(self, **kwargs):
        kwargs = super(AssignAgentView, self).get_form_kwargs(**kwargs)
        kwargs.update({
             "request": self.request
        })
        return kwargs

    def get_success_url(self):
        return reverse("leads:home")
    
    def form_valid(self, form):
        agent = form.cleaned_data["agent"]
        lead = Lead.objects.get(id=self.kwargs["pk"])
        lead.agent = agent
        lead.save()
        return super(AssignAgentView, self).form_valid(form)


class CategoryListView(LoginRequiredMixin, generic.ListView):
    template_name ="Lead/list_category.html"
    context_object_name ="category_list"


    def get_context_data(self, **kwargs):
        context = super(CategoryListView, self).get_context_data(**kwargs)
        user = self.request.user
        if user.is_organisor:
            queryset = Lead.objects.filter(organisation=user.userprofile)
        else:
            queryset = Lead.objects.filter(organisation=user.agent.organisation)
        context.update({
            "unassigned_lead_count": queryset.filter(category__isnull=True).count()
        })    
        return context



    def get_queryset(self):
        user = self.request.user
        if user.is_organisor:
            queryset = Category.objects.filter(organisation=user.userprofile)
        else:
            queryset = Category.objects.filter(organisation=user.agent.organisation)
        
        return queryset




class CategoryDetailView(LoginRequiredMixin, generic.DetailView):
    template_name ="Lead/detail_category.html"
    context_object_name = "category"

    def get_queryset(self):
         user = self.request.user
         if user.is_organisor:
            queryset = Category.objects.filter(organisation=user.userprofile)
         else:
            queryset = Category.objects.filter(organisation=user.agent.organisation)
            
         return queryset


class FollowUpCreateView(OrganisorAndLoginRequiredMixin, generic.CreateView):
    template_name = "Lead/followup_create.html/"
    form_class = FollowUpModelForm
    def get_success_url(self):
        return reverse("leads:detail-lead",kwargs={"pk":self.kwargs["pk"]})
    def get_context_data(self, **kwargs):
        context = super(FollowUpCreateView, self).get_context_data(**kwargs)

        context.update({
           "lead": Lead.objects.get(pk=self.kwargs["pk"]) 
        })
        return context
    def form_valid (self, form):
        lead = Lead.objects.get(pk=self.kwargs["pk"])
        followup = form.save(commit=False) 
        followup.lead =lead
        followup.save()
        return super(FollowUpCreateView, self).form_valid(form)



class FollowUpUpdateView(LoginRequiredMixin, generic.UpdateView):
    template_name = "Lead/followup_update.html/"
    form_class = FollowUpModelForm

    def get_success_url(self):
        return reverse("leads:detail-lead",kwargs={"pk":self.get_object().lead.id})
    def get_queryset(self):
        user = self.request.user
        if user.is_organisor:
            queryset = FollowUp.objects.filter(lead__organisation=user.userprofile)
        else:
            queryset = FollowUp.objects.filter(lead__organisation=user.agent.organisation)
            
        return queryset
       


class FollowUpDeleteView(LoginRequiredMixin, generic.DeleteView):
    template_name = "Lead/delete_followup.html/"
    def get_success_url(self):
        return reverse("leads:detail-lead",kwargs={"pk":self.get_object().lead.id})
    def get_queryset(self):
        user = self.request.user
        if user.is_organisor:
            queryset = FollowUp.objects.filter(lead__organisation=user.userprofile)
        else:
            queryset = FollowUp.objects.filter(lead__organisation=user.agent.organisation)
        return queryset



############################################# BASIC WAY TO CREATE VIEWS FUNCTIONS #####################################


# #############################################################  CREATE LEAD
# def lead_create (request):
#     form = LeadModelForm()
#     if request.method == "POST":
#         form = LeadModelForm(request.POST)
#         if form.is_valid():
#             form.save()
#             return redirect("/leads")

#     context = {
#         "form" : form
#     }
#     return render(request, "Lead/lead_create.html", context)


# ##############################################################   LEAD DETAILS
# # def lead_detail (request, id):
# #     lead = Lead.objects.get(id=id)
# #     context = {
# #         "lead" : lead
# #     }
# #     return render(request, "Lead/lead_detail.html", context)



# ##############################################################   UPDATE LEAD
# def update_lead (request, id):
#     lead = Lead.objects.get(id=id)
#     form = LeadModelForm(instance=lead)
#     if request.method == "POST":
#         form = LeadModelForm(request.POST, instance=lead)
#         if form.is_valid():
#             form.save()
#             return redirect("/leads")
#     context = {
#         "form" : form,
#         "lead" : lead
#     }
#     return render(request, "Lead/update_lead.html", context)



# ################################################################## DELETE LEAD
# def delete_lead (request, id):
#     lead = Lead.objects.get(id=id)
#     form = LeadModelForm(instance=lead)
#     if request.method == "POST":
#         form = LeadModelForm(request.POST, instance=lead)
#         if form.is_valid():
#             lead.delete()
#             return redirect("/leads")
#     context = {
#         "form" : form,
#         "lead" : lead
#     }
#     return render(request, "Lead/delete_lead.html", context)