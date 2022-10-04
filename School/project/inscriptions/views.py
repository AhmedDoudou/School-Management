import datetime
from django.shortcuts import get_object_or_404
from django.shortcuts import render, redirect, reverse
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.contrib.auth.views import LoginView  
from django.views.generic import CreateView, ListView, UpdateView, DeleteView, TemplateView
from inscriptions.models import *
from core.forms import *
from django.core.files.storage import FileSystemStorage



class InscriptionCreateView(LoginRequiredMixin,CreateView):
    template_name = "inscription/create.html/"
    model = Inscriptions
    fields = ("__all__")
    success_url ="/"
    def get_context_data(self, **kwargs):
        context = super(InscriptionCreateView,self).get_context_data(**kwargs)
        page_title = 'New Inscription'
        context.update({
            "page_title":page_title
         })
        return context


def NewInscriptionCreateView(request):
    template_name = "inscription/newinscription.html"
    program = Programs.objects.all()
    subscription =Subscriptions.objects.all()

    if request.method == 'POST':
        fullname = request.POST.get("fullname")
        phone = request.POST.get("phone")
        gender = request.POST.get("gender_p")
        desc = request.POST.get("description")
        address = request.POST.get("address")
        # parent
        parent = Contacts(address=address,description=desc,fullname=fullname,gender=gender,phone=phone)
        # parent.save()

        first_name  = request.POST.get("first_name")
        last_name   = request.POST.get("last_name")
        level       = request.POST.get("level")
        age         = request.POST.get("age")
        # status = request.POST.get("status")
        email   = request.POST.get("email")
        gender  = request.POST.get("gender")
        if request.FILES['picture']:
            picture     = request.FILES['picture']
            fss         = FileSystemStorage()
            file        = fss.save(picture.name,picture)
            picture_url = fss.url(file)
        address         = request.POST.get("address")

        # student
        student         = Students(picture=picture_url,gender=gender,email=email,first_name=first_name,last_name=last_name,level=level,age=age,address=address,)
        # student.save()
        program_id      = request.POST.get("program")
        subscription_id = request.POST.get("subscription")
        last_student    = Students.objects.last()
        last_contact    = Contacts.objects.last()
        program         = Programs.objects.get(id=program_id)

        subscription    = Subscriptions.objects.get(pk=subscription_id)
        inscription     = Inscriptions(parent=last_contact,student=last_student,program=program,subscription=subscription)
        inscription.save()


    
    context = {
        'program': program,
        'subscription': subscription
    }

    return render(request, template_name, context)

     

class InscriptionListView(LoginRequiredMixin, ListView):
    template_name = "inscription/list.html"
    context_object_name = "list"
    model = Inscriptions
    def get_context_data(self, **kwargs):
        context = super(InscriptionListView,self).get_context_data(**kwargs)
        page_title = 'Inscription List'
        context.update({
            "page_title":page_title
         })
        return context
    
    
class InscriptionUpdateView(LoginRequiredMixin, UpdateView):
    model = Inscriptions
    template_name = "inscription/update.html"
    fields = '__all__'
    success_url ="/"
    def get_context_data(self, **kwargs):
        context = super(InscriptionUpdateView,self).get_context_data(**kwargs)
        page_title = 'Update Inscription'
        context.update({
            "page_title":page_title
         })
        return context


class InscriptionDeleteView(LoginRequiredMixin, DeleteView):
    model = Inscriptions
    template_name = "inscription/delete.html"
    context_object_name = "delete"
    success_url ="/list"
 
# def InscView(request):
#     template_name = "inscription/insc.html/"


#     student_form = StudentForm
#     contact_form = ContactForm
#     program = Programs.objects.all()
#     subscription =Subscriptions.objects.all()

#     if request.method == "POST":
#         student_form = StudentForm(request.POST)
#         contact_form = ContactForm(request.POST)
#         if student_form.is_valid() :
#             student = self.form_save(student_form)
#             contact_form.is_valid()
#             contact = contact_form.save()
#     last_student = Students.objects.last()
#     last_contact = Contacts.objects.last()
#     context = {
#         'student_form': student_form,
#         'contact_form': contact_form,
#         'program': program,
#         'subscription': subscription

#     }
#     return render(request, template_name,context)


    #         print(student)
    #         inscription = inscription_form(student=student,parent=contact)
    #         if  inscription_form.is_valid():
    #             inscription.save()
    # context = {'student_form': student_form,
    #             'contact_form': contact_form,
    #             'inscription_form': inscription_form
    # }
    # return render(request,"inscription/newinscription.html/",context)


    # student_form_class = StudentForm
    # contact_form_class = ContactForm
    # inscription_form_class = InscriptionForm
    
    # def post(self, request):
    #     post_data = request.POST or None
    #     inscription_form = self.inscription_form_class(post_data, prefix='inscription')
    #     student_form = self.student_form_class(post_data, prefix='student')
    #     contact_form = self.contact_form_class(post_data, prefix='contact')
    #     print('Printing POST:',request.POST)
        
    #     context = self.get_context_data(
    #         student_form=student_form,
    #         contact_form=contact_form,
    #         inscription_form=inscription_form,
            
    #         )
    #     if student_form.is_valid() and contact_form.is_valid():
    #         student = self.form_save(student_form)
    #         parent = self.form_save(contact_form)
    #         inscription = self.form_save(inscription_form(student=student,parent=parent))

        

       
        
    #     return self.render_to_response(context) 

    # def form_save(self, form):
        
    #     obj = form.save()
    #     return obj

    # def get(self, request, *args, **kwargs):
    #     return self.post(request, *args, **kwargs)
    # success_url ="http://127.0.0.1:8000/"