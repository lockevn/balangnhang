using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Authentication
{
    public interface IUser
    {
        string Username { get; set; }
        string Authkey { get; set; }
        string System { get; set; }
        string FullIdentity { get; set; }
    }
}
