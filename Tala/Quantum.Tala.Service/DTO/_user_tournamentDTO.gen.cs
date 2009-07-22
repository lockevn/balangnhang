using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for user_tournamentDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("playing_user_tournament","id",true)]
	 public sealed partial class user_tournamentDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string U_FLD = "u";
		public const string TOURID_FLD = "tourid";
		public const string STATUS_FLD = "status";
		public const string DESC_FLD = "desc";
		public const string POINT_FLD = "point";
		public const string TRANSACTIONID_FLD = "transactionid";
		public const string DT_FLD = "dt";

		private int m_nid;
		private string m_su;
		private int m_ntourid;
		private int m_nstatus;
		private string m_sdesc;
		private int m_npoint;
		private int m_ntransactionid;
		private object m_odt;

		public user_tournamentDTO()
		{
		}
		
		[PersistenceProperty("id",true,true)]
		public int id
		{
			get
			{
				return m_nid;
			}
			set
			{
				m_htIsNull["id"] = false;
				m_nid = value;
			}
		}
		
		[PersistenceProperty("u",false,false)]
		public string u
		{
			get
			{
				return m_su;
			}
			set
			{
				m_htIsNull["u"] = false;
				m_su = value;
			}
		}
		
		[PersistenceProperty("tourid",false,false)]
		public int tourid
		{
			get
			{
				return m_ntourid;
			}
			set
			{
				m_htIsNull["tourid"] = false;
				m_ntourid = value;
			}
		}
		
		[PersistenceProperty("status",false,false)]
		public int status
		{
			get
			{
				return m_nstatus;
			}
			set
			{
				m_htIsNull["status"] = false;
				m_nstatus = value;
			}
		}
		
		[PersistenceProperty("desc",false,false)]
		public string desc
		{
			get
			{
				return m_sdesc;
			}
			set
			{
				m_htIsNull["desc"] = false;
				m_sdesc = value;
			}
		}
		
		[PersistenceProperty("point",false,false)]
		public int point
		{
			get
			{
				return m_npoint;
			}
			set
			{
				m_htIsNull["point"] = false;
				m_npoint = value;
			}
		}
		
		[PersistenceProperty("transactionid",false,false)]
		public int transactionid
		{
			get
			{
				return m_ntransactionid;
			}
			set
			{
				m_htIsNull["transactionid"] = false;
				m_ntransactionid = value;
			}
		}
		
		[PersistenceProperty("dt",false,false)]
		public object dt
		{
			get
			{
				return m_odt;
			}
			set
			{
				m_htIsNull["dt"] = false;
				m_odt = value;
			}
		}

		public string IsNullSerializedString
		{
			get
			{
				return GetIsNullSerializedString();
			}
			set
			{
				SetIsNullSerializedString(value);
			}
		}
	}
}
