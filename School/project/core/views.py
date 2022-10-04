import datetime
from django.shortcuts import render, redirect, reverse, get_object_or_404
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.contrib.auth.views import LoginView  
from django.views.generic import CreateView, ListView, UpdateView, DeleteView, DetailView
from inscriptions.models import *
from .forms import *
from formtools.wizard.views import SessionWizardView
from django.core.files.storage import FileSystemStorage
from django.http import HttpResponse
from django.template.loader import get_template
from xhtml2pdf import pisa



class DashbordView( generic.TemplateView):
    template_name = "dashboard.html"
    def get_context_data(self, **kwargs):
        context = super(DashbordView, self).get_context_data(**kwargs)
        page_title = 'Dashboard'
        # counting Total of students
        total_students = Students.objects.all().count()
         # students in 30 days ago
        thirty_days = datetime.date.today() - datetime.timedelta(days=30)
        last_thirty_days = Students.objects.filter(date_added__gte=thirty_days).count()
        sub_students = Inscriptions.objects.all().count()
        # All models
        student = Students.objects.all()
        program = Programs.objects.all()
        contact = Contacts.objects.all()
        inscription = Inscriptions.objects.all()
        subscription = Subscriptions.objects.all()
        context.update({
            "total_students": total_students,
            "last_thirty_days": last_thirty_days,
            "sub_students": sub_students,
            "student": student,
            "program": program,
            "inscription":inscription,
            "contact":contact,
            "subscription": subscription,
            "page_title":page_title
         })
        return context


class Login(LoginView):
    template_name = "registration/login.html"
    def get_context_data(self, **kwargs):
        context = super(Login,self).get_context_data(**kwargs)
        page_title = 'Login'
        context.update({
            "page_title":page_title
         })
        return context


class StudentCreateView(LoginRequiredMixin,CreateView):
    template_name = "student/create.html/"
    model = Students
    fields = ("__all__")
    success_url ="/"
    def get_context_data(self, **kwargs):
        context = super(StudentCreateView,self).get_context_data(**kwargs)
        page_title = 'Add Student'
        context.update({
            "page_title":page_title
         })
        return context
    

class StudentListView(LoginRequiredMixin, ListView):
    template_name = "student/list.html"
    context_object_name = "student/list"
    model = Students
    def get_context_data(self, **kwargs):
        context = super(StudentListView,self).get_context_data(**kwargs)
        page_title = 'Students List'
        context.update({
            "page_title":page_title
         })
        return context
    
    
class StudentUpdateView(LoginRequiredMixin, UpdateView):
    model = Students
    template_name = "student/update.html"
    fields = '__all__'
    success_url ="/"
    def get_context_data(self, **kwargs):
        context = super(StudentUpdateView,self).get_context_data(**kwargs)
        page_title = 'Update Student'
        context.update({
            "page_title":page_title
         })
        return context


class StudentDeleteView(LoginRequiredMixin, DeleteView):
    model = Students
    template_name = "student/delete.html"
    context_object_name = "delete"
    success_url ="/list"
    

class StudentDetailView(LoginRequiredMixin, DetailView):
    model = Students
    template_name = "student/detail.html"
    context_object_name = "detail"
    success_url ="/"



class FormWizardView(SessionWizardView):
    template_name = "test/index.html"
    form_list = [StudentForm, ContactForm, InscriptionForm]
    file_storage = FileSystemStorage(location=settings.MEDIA_ROOT)

    def done(self, form_list, **kwargs):
        return render(self.request, 'dashboard.html', {
            'form_data': [form.cleaned_data for form in form_list],
        })


def student_render_pdf_view(request,*args,**kwargs):
    pk = kwargs.get('pk')
    student = get_object_or_404(Students, pk=pk)

    template_path = 'student/detail.html'
    context = {'student': student}
    # Create a Django response object, and specify content_type as pdf
    response = HttpResponse(content_type='application/pdf')
    response['Content-Disposition'] = ' filename="report.pdf"'
    # find the template and render it.
    template = get_template(template_path)
    html = template.render(context)

    # create a pdf
    pisa_status = pisa.CreatePDF(
       html, dest=response)
    # if error then show some funny view
    if pisa_status.err:
       return HttpResponse('We had some errors <pre>' + html + '</pre>')
    return response


def render_pdf_view(request):
    template_path = 'inscription/receive.html'
    context = {'myvar': 'this is your template context'}
    # Create a Django response object, and specify content_type as pdf
    response = HttpResponse(content_type='application/pdf')
    response['Content-Disposition'] = ' filename="report.pdf"'
    # find the template and render it.
    template = get_template(template_path)
    html = template.render(context)

    # create a pdf
    pisa_status = pisa.CreatePDF(
       html, dest=response)
    # if error then show some funny view
    if pisa_status.err:
       return HttpResponse('We had some errors <pre>' + html + '</pre>')
    return response

