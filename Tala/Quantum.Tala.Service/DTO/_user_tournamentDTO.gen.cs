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
	[PersistenceClass("game_user_tournament","id",true)]
	 public sealed partial class user_tournamentDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string U_FLD = "u";
		public const string TOURNAMENTID_FLD = "tournamentid";
		public const string NUMBEROFATTENTION_FLD = "numberofattention";
		public const string STATUS_FLD = "status";
		public const string DESC_FLD = "desc";
		public const string TRANSACTIONID_FLD = "transactionid";
		public const string WIN_FLD = "win";
		public const string LOSE_FLD = "lose";
		public const string POINT_FLD = "point";
		
		

		private int m_nid;
		private string m_su;
		private int m_ntournamentid;
		private int m_nnumberofattention;
		private int m_nstatus;
		private string m_sdesc;
		private int m_ntransactionid;
		private int m_nwin;
		private int m_nlose;
		private int m_npoint;
		
		
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
		
		[PersistenceProperty("tournamentid",false,false)]
		public int tournamentid
		{
			get
			{
				return m_ntournamentid;
			}
			set
			{
				m_htIsNull["tournamentid"] = false;
				m_ntournamentid = value;
			}
		}
		
		[PersistenceProperty("numberofattention",false,false)]
		public int numberofattention
		{
			get
			{
				return m_nnumberofattention;
			}
			set
			{
				m_htIsNull["numberofattention"] = false;
				m_nnumberofattention = value;
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
		
		[PersistenceProperty("win",false,false)]
		public int win
		{
			get
			{
				return m_nwin;
			}
			set
			{
				m_htIsNull["win"] = false;
				m_nwin = value;
			}
		}
		
		[PersistenceProperty("lose",false,false)]
		public int lose
		{
			get
			{
				return m_nlose;
			}
			set
			{
				m_htIsNull["lose"] = false;
				m_nlose = value;
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
