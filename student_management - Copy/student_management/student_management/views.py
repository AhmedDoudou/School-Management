from django.contrib.auth import login, logout, authenticate
from django.http import HttpResponse
from django.shortcuts import render, redirect
from app.EmailBackend import EmailBackend
from app.models import CustomUser, Student, Session, Course, Inscription,Program, Membership, Registration
from django.contrib import messages
from django.core.files.storage import FileSystemStorage



def LOGIN(req):
    return render(req, 'login.html' )


def INDEX(req):
    memberships = Membership.objects.all()
    programs = Program.objects.all();
    students = Student.objects.all();
    context={
        'programs': programs,
        'memberships': memberships,
        'students' : students,
    } 
    return render(req, 'Student/students.html', context )


def doLogin(request):
    if request.method == "POST":
        user = EmailBackend.authenticate(request, username = request.POST.get('email'),
                                         password = request.POST.get('password'))
        if user is not None:
            login(request, user)
            user_type= user.user_type
            if user_type =='1':
                return redirect('hod_home')
            if user_type =='2':
                return HttpResponse("This the Staff Panel")
            elif user_type =='3':
                return HttpResponse("This the Student Panel")
            else:
                # message
                messages.error(request, 'Email or password are invalid')
                return redirect('login')
        else:
            # message
            messages.error(request, 'Email or password are invalid')
            return redirect('login')

def doLogout(request):
   logout(request)
   return redirect('login')


def PROFILE(request):
    user = CustomUser.objects.get(id=request.user.id)
    context ={
        'user':user
    }
    return render(request,'profile.html', context)


def PROFILE_UPDATE(request):
    if request.method == "POST":
        username = request.POST.get("username")
        email = request.POST.get("email")
        first_name = request.POST.get("first_name")
        last_name = request.POST.get("last_name")
        password  =request.POST.get("password")
        profile_pic = request.FILES.get("profile_pic")
        try:
            customuser = CustomUser.objects.get(id=request.user.id)
            customuser.first_name = first_name
            customuser.last_name = last_name
            customuser.email = email
            customuser.profile_pic = profile_pic
            if password !=None and password != "":
                customuser.set_password(password)
            customuser.save()
            messages.success(request, "Your profile updated Successfully")
            redirect('profile')


        except:
            messages.error(request, "Failed to update Your profile.")

    return render(request, "profile.html")



    return None

def STUDENT_DETAIL(req, id):
    student = Student.objects.get(id = id)
    #name = Inscription.objects.get(student)
    #program = Student.objects.filter(first_name = name)
    context = {
        'student': student
    }
    return render(req, 'Student/student_detail.html', context)
def STUDENT_ADD(request):
    courses = Course.objects.all();
    #print(courses[len(courses)-1].id)
    context = {
        'courses': courses
    }
    return render(request, 'Student/add_student.html', context)



def STUDENT_REGISTER(req):
    memberships = Membership.objects.all()
    programs = Program.objects.all();
    students = Student.objects.all();
    last_student = Student.objects.last();
    payment_methods = ["CASH", "CHEQUE", "CREDIT CARD"]
    context={
        'programs': programs,
        'memberships': memberships,
        'students' : students,
        'last_student':last_student,
        'payment_methods':payment_methods,
    }
    return render(req, 'Student/new_inscription.html', context)


def REGISTRATION_SAVE(req):
    if req.method == 'POST':
        program_id = req.POST.get('program_id')
        student_id = req.POST.get('student_id')
        membership_id = req.POST.get('membership_id')
        payment_method = req.POST.get('payment_method')
        membership = Membership.objects.get(id = membership_id)
        student = Student.objects.get(id = student_id)
        program = Program.objects.get(id = program_id)
        # last_student = Student.objects.last()
        registration = Registration(student =student,program=program, membership= membership, payment_method =payment_method )
        registration.save()

    return redirect('students')

def STUDENT_SUBSCRIBE(request):
    memberships = Membership.objects.all()
    programs = Program.objects.all();
    students = Student.objects.all();
    last_student = Student.objects.last();
    context={
        'programs': programs,
        'memberships': memberships,
        'students' : students,
        'last_student':last_student,
    }
    return render(request, 'Student/new_inscription.html', context)

def DELETE(req):
    Student.objects.all().delete()
    return redirect('hod_home')
def Dashboard(req):
    return render(req, 'Hod/home.html')


def STUDENT_SAVE(req):
    if req.method == 'POST':
        first_name = req.POST.get('first_name')
        last_name = req.POST.get('last_name')
        grade = req.POST.get('grade')
        mobile = req.POST.get('mobile')
        email = req.POST.get('email')
        birthday = req.POST.get('birthday')
        address = req.POST.get('address')
        if req.FILES['profile_pic']:
            picture     = req.FILES['profile_pic']
            fss         = FileSystemStorage()
            file        = fss.save(picture.name,picture)
            picture_url = fss.url(file)
        student = Student(first_name = first_name, last_name=last_name, grade = grade, mobile= mobile, email = email, address=address, birthday=birthday,profile_pic = picture_url)
        student.save()

    return redirect('students')

def STUDENT_Modal(req):

    if req.method == 'POST':
        first_name = req.POST.get('first_name')
        last_name = req.POST.get('last_name')
        grade = req.POST.get('grade')
        mobile = req.POST.get('mobile')
        email = req.POST.get('email')
        birthday = req.POST.get('birthday')
        address = req.POST.get('address')
        student = Student(first_name = first_name, last_name=last_name, grade = grade, mobile= mobile, email = email, address=address, birthday=birthday)
        student.save()

    return redirect('student_subscribe')




